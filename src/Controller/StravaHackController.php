<?php

namespace Olz\Controller;

use Olz\Anniversary\Endpoints\RunEndpointTrait;
use Olz\Entity\Anniversary\RunRecord;
use Olz\Entity\Users\User;
use Olz\Parsers\StravaActivityParser;
use Olz\Utils\WithUtilsTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StravaHackController extends AbstractController {
    use WithUtilsTrait;
    use RunEndpointTrait;

    #[Route('/api-cors/registerStravaRun', methods: ['POST', 'OPTIONS'])]
    public function registerStravaRun(Request $request): JsonResponse {
        // Handle preflight OPTIONS
        if ($request->getMethod() === 'OPTIONS') {
            $response = new JsonResponse([], 204); // No Content
            $this->populateCors($request, $response);
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Cache-Control', 'max-age=3600');
            return $response;
        }

        // Handle POST
        if ($request->getMethod() === 'POST') {
            $json_content = $request->getContent();
            $payload = json_decode($json_content, true);

            $token = $payload['token'] ?? null;
            $key = $this->envUtils()->getEncryptionKey('strava-hack-token');
            $data = $this->generalUtils()->decrypt($key, $token);
            $user_id = $data['user_id'] ?? null;
            if (!$data || !$user_id || !isset($data['token_time'])) {
                return new JsonResponse(['msg' => "🚫 Ungültiger Token"], 400);
            }
            $user_repo = $this->entityManager()->getRepository(User::class);
            $user = $user_repo->findOneBy(['id' => $user_id]);
            if (!$user) {
                $this->log()->notice("registerStravaRun denied for invalid user ID {$user_id}");
                return new JsonResponse(['msg' => "🚫 Token von ungültigem Benutzer"], 400);
            }
            // TODO: Check token time after September
            $this->log()->debug("registerStravaRun: {$json_content}");

            $activityId = $payload['activityId'] ?? null;
            if (!$activityId || $activityId < 100000000 || $activityId > 100000000000000) {
                $this->log()->notice("registerStravaRun denied for activityId {$activityId} by {$user}");
                return new JsonResponse(['msg' => "🚫 Ungültige Aktivitäts-ID"], 400);
            }
            $source = "strava-id{$activityId}";
            $runs_repo = $this->entityManager()->getRepository(RunRecord::class);
            $run = $runs_repo->findOneBy(['source' => $source]);
            $is_update = (bool) $run;

            $html = $payload['html'] ?? null;
            $this->generalUtils()->checkNotNull($html, 'HTML must be provided');
            $parser = new StravaActivityParser();
            $data = $parser->parse_strava_activity_html($html);
            if (
                !$data['name']
                || !$data['sportType']
                || !$data['runAt']
                || !$data['distanceMeters']
                || !$data['elevationMeters']
            ) {
                $enc_data = json_encode($data);
                $this->log()->notice("registerStravaRun parse error for activityId {$activityId} by {$user}: {$enc_data}");
                return new JsonResponse(['msg' => "🚫 HTML konnte nicht (vollständig) gelesen werden."], 400);
            }
            $name_arr = explode(' ', $data['name']);
            $name = $name_arr[0].' '.implode(' ', array_map(
                fn ($part) => substr($part, 0, 1).'.',
                array_slice($name_arr, 1),
            ));
            $sport_type = $data['sportType'];
            $is_sport_type_valid = [
                'Run' => true,
                'TrailRun' => true,
                'Hike' => true,
                'Walk' => true,
                'Laufen' => true,
                'Traillauf' => true,
                'Wanderung' => true,
                'Spaziergang' => true,
            ];
            $is_counting = $is_sport_type_valid[$sport_type] ?? false;

            if (!$run) {
                $run = new RunRecord();
                $this->entityUtils()->createOlzEntity($run, ['ownerUserId' => $user_id, 'onOff' => true]);
            } else {
                $this->entityUtils()->updateOlzEntity($run, ['ownerUserId' => $user_id, 'onOff' => true]);
            }
            if ($is_update) {
                $old_data = $this->getEntityData($run);
                $this->log()->notice('OLD:', [$old_data]);
            }
            $run->setUser(null);
            $run->setRunnerName($name);
            $run->setRunAt($data['runAt']);
            $run->setIsCounting($is_counting);
            $run->setDistanceMeters(intval($data['distanceMeters']));
            $run->setElevationMeters(intval($data['elevationMeters']));
            $run->setSportType($sport_type);
            $run->setSource($source);
            $run->setInfo(json_encode($data) ?: null);
            if ($is_update) {
                $new_data = $this->getEntityData($run);
                $this->log()->notice('NEW:', [$new_data]);
            }
            $this->entityManager()->persist($run);
            $this->entityManager()->flush();

            $msg = $is_update ? '✅ Existierender Eintrag wurde aktualisiert' : '✅ Neuer Eintrag wurde erstellt';
            $response = new JsonResponse(['msg' => $msg], 200);
            $this->populateCors($request, $response);
            return $response;
        }

        throw new \Exception("Tertium non datur!");
    }

    protected function populateCors(Request $request, Response $response): void {
        $allowedOrigins = ['https://www.strava.com'];
        $origin = $request->headers->get('Origin');
        if ($origin && in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
    }
}

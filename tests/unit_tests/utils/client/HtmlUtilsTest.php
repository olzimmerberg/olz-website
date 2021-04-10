<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/client/HtmlUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \HtmlUtils
 */
final class HtmlUtilsTest extends UnitTestCase {
    public function testSanitize(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>MailTo("e.mail+test", "olzimmerberg.ch", "E-Mail")</script>',
            $html_utils->sanitize('e.mail+test@olzimmerberg.ch')
        );
    }

    public function testReplacePureEmailAdresses(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>MailTo("e.mail+test", "olzimmerberg.ch", "E-Mail")</script>',
            $html_utils->replaceEmailAdresses('e.mail+test@olzimmerberg.ch')
        );
        $this->assertSame(
            'Mail: <script>MailTo("e.mail+test", "olzimmerberg.ch", "E-Mail")</script>.',
            $html_utils->replaceEmailAdresses('Mail: e.mail+test@olzimmerberg.ch.')
        );
        $this->assertSame(
            'Mails: <script>MailTo("e.mail+test", "olzimmerberg.ch", "E-Mail")</script>, <script>MailTo("e.mail", "olzimmerberg.ch", "E-Mail")</script>.',
            $html_utils->replaceEmailAdresses('Mails: e.mail+test@olzimmerberg.ch, e.mail@olzimmerberg.ch.')
        );
    }

    public function testReplaceMailToLinksWithoutSubject(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>MailTo("e.mail+test", "olzimmerberg.ch", "Test")</script>',
            $html_utils->replaceEmailAdresses('<a href="mailto:e.mail+test@olzimmerberg.ch">Test</a>')
        );
        $this->assertSame(
            'Mail: <script>MailTo("e.mail+test", "olzimmerberg.ch", "Contact me")</script>!',
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:e.mail+test@olzimmerberg.ch" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            'Mails: <script>MailTo("e.mail+test", "olzimmerberg.ch", "Contact me")</script> <script>MailTo("e.mail", "olzimmerberg.ch", "Contact me")</script>!',
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:e.mail+test@olzimmerberg.ch" class="linkmail">Contact me</a> <a name="" href="mailto:e.mail@olzimmerberg.ch">Contact me</a>!')
        );
    }

    public function testReplaceMailToLinksWithSubject(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>MailTo("e.mail+test", "olzimmerberg.ch", "Test", "test")</script>',
            $html_utils->replaceEmailAdresses('<a href="mailto:e.mail+test@olzimmerberg.ch?subject=test">Test</a>')
        );
        $this->assertSame(
            'Mail: <script>MailTo("e.mail+test", "olzimmerberg.ch", "Contact me", "another%20test")</script>!',
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:e.mail+test@olzimmerberg.ch?subject=another%20test" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            'Mails: <script>MailTo("e.mail+test", "olzimmerberg.ch", "Contact me", "another%20test")</script>, <script>MailTo("e.mail", "olzimmerberg.ch", "Contact me", "another%20test")</script>!',
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:e.mail+test@olzimmerberg.ch?subject=another%20test" class="linkmail">Contact me</a>, <a name="" href="mailto:e.mail@olzimmerberg.ch?subject=another%20test">Contact me</a>!')
        );
    }
}

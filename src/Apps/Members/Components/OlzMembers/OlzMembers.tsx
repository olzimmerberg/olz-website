import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi, OlzApiRequests, OlzMemberInfo} from '../../../../Api/client';
import {OlzMultiFileField} from '../../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {dataHref} from '../../../../Utils/constants';
import {getResolverResult} from '../../../../Utils/formUtils';
import {initOlzUserInfoModal} from '../../../../Users';

import './OlzMembers.scss';

type ImportState = 'IDLE' | 'UPLOADING' | 'IMPORTING' | 'IMPORTED';

const PANIC_NOTICE = '⚠️ Falls du einen Fehler gemacht hast, mache einfach einen neuen, korrekten Import!';
const LABEL_BY_ACTION = {
    CREATE: '✨ Eintritt',
    KEEP: '🟰 Unverändert',
    UPDATE: '♻️ Aktualisiert',
    DELETE: '🚫 Austritt',
};
const EXPLANATION_BY_ACTION = {
    CREATE: `Dieses Mitglied war im letzten Import noch nicht vorhanden. Der Eintrag wurde in der externen Benutzerverwaltung seit dem letzten Import neu erstellt.\n${PANIC_NOTICE}`,
    KEEP: 'Dieses Mitglied war bereits im letzten Import vorhanden. Der Eintrag blieb in der externen Benutzerverwaltung seither unverändert.',
    UPDATE: `Dieses Mitglied war bereits im letzten Import vorhanden. Der Eintrag wurde aber seither in der externen Benutzerverwaltung bearbeitet.\n${PANIC_NOTICE}`,
    DELETE: `Dieses Mitglied war im letzten Import vorhanden, fehlt aber in diesem Import. Der Eintrag wurde also in der externen Benutzerverwaltung seit dem letzten Import gelöscht.\n${PANIC_NOTICE}`,
};

interface OlzMembersImportForm {
    csvFileIds: string[];
}

const resolver: Resolver<OlzMembersImportForm> = async (values) => {
    const errors: FieldErrors<OlzMembersImportForm> = {};
    return getResolverResult(errors, values);
};

function getApiFromForm(formData: OlzMembersImportForm): OlzApiRequests['importMembers'] {
    return {
        csvFileId: formData.csvFileIds?.[0] ?? null,
    };
}

// ---

export const OlzMembers = (): React.ReactElement => {
    const {handleSubmit, formState: {errors}, control} = useForm<OlzMembersImportForm>({
        resolver,
        defaultValues: {
            csvFileIds: [],
        },
    });

    const [isUploading, setIsUploading] = React.useState<boolean>(false);
    const [status, setStatus] = React.useState<ImportState>('IDLE');
    const [members, setMembers] = React.useState<OlzMemberInfo[] | null>(null);
    const [exportCsv, setExportCsv] = React.useState<string | null>(null);

    const onSubmit: SubmitHandler<OlzMembersImportForm> = async (values) => {
        const data = getApiFromForm(values);
        const [err, response] = await olzApi.getResult('importMembers', data)
           ;
        if (err || response.status !== 'OK') {
            return;
        }
        setMembers(response.members);
        setStatus('IMPORTED');
    };

    React.useEffect(() => {
        if (status === 'IDLE' && isUploading) {
            setStatus('UPLOADING');
        }
        if (status === 'UPLOADING' && !isUploading) {
            handleSubmit(onSubmit)();
            setStatus('IMPORTING');
        }
    }, [isUploading, status]);

    const onExport = async () => {
        const [err, response] = await olzApi.getResult('exportMembers', {})
           ;
        if (err || response.status !== 'OK') {
            return;
        }
        setExportCsv(response.csvFileId ?? null);
    };

    let content: React.ReactNode = '';
    switch (status) {
        case 'IDLE':
        case 'UPLOADING':
            content = (
                <div id='import-upload'>
                    <ul>
                        <li>Gehe zu <a href='https://app.clubdesk.com/clubdesk/start' target='_blank'>Clubdesk</a></li>
                        <li>Gehe zu "Kontakte und Gruppen" -&gt; "Alle Kontakte"</li>
                        <li>Wähle "Export"</li>
                        <li>Unter "Zeilen:", <b>wähle "Alle Zeilen" ⚠️</b></li>
                        <li>Unter "Spalten:", <b>wähle "Alle Spalten" ⚠️</b></li>
                        <li>Unter "Format:", wähle "CSV (Excel)"</li>
                    </ul>
                    <hr/>
                    <OlzMultiFileField
                        title='CSV Import-Datei'
                        name='csvFileIds'
                        errors={errors}
                        control={control}
                        setIsLoading={setIsUploading}
                    />
                </div>
            );
            break;
        case 'IMPORTING':
            content = 'Daten werden importiert... Bitte warten.';
            break;
        case 'IMPORTED': {
            const memberRows = members?.map((member, index) => {
                const getMemberDisplay = () => {
                    if (member.username) {
                        return (<span title='Die Benutzer-Id, wie in der externen Benutzerverwaltung eingegeben.'>
                            {member.username}
                        </span>);
                    }
                    if (member.firstName && member.lastName) {
                        return (<span title='In der externen Benutzerverwaltung wurde keine Benutzer-Id eingegeben.'>
                            ⚠️ {member.firstName} {member.lastName}
                        </span>);
                    }
                    if (member.company) {
                        return (<span title='In der externen Benutzerverwaltung wurde keine Benutzer-Id eingegeben.'>
                            ⚠️ {member.company}
                        </span>);
                    }
                    return (<span title='In der externen Benutzerverwaltung wurde keine Benutzer-Id, kein Vor- und Nachname, und keine Firma eingegeben.'>
                        ⚠️ Ident: {member.ident}
                    </span>);
                };
                const updates = Object.keys(member.updates).map((key) => (
                    <div>
                        {key}:&nbsp;
                        <span className='old'>"{member.updates[key].old}"</span>
                        &nbsp;➡️&nbsp;
                        <span className='new'>"{member.updates[key].new}"</span>
                    </div>
                ));
                return (<tr id={`row-${index}`}>
                    <td
                        className='username'
                        title='Benutzer, wie in der externen Benutzerverwaltung eingegeben.'
                    >
                        {getMemberDisplay()}
                    </td>
                    <td className='user-info'>{(member.user
                        ? (
                            <a
                                href='#'
                                onClick={() => initOlzUserInfoModal(member.user?.id ?? 0)}
                                className='olz-user-info-modal-trigger'
                            >
                                {member.user.firstName} {member.user.lastName}
                            </a>
                        ) : (member.matchingUsername
                            ? (
                                <span title='Vorname und Name passen, aber die Benutzer-Id nicht! Aktualisiere die Benutzer-Id in der externen Benutzerverwaltung, um dieses OLZ-Konto zu verlinken.'>
                                    ➡️&nbsp;
                                    <input
                                        type='text'
                                        readOnly
                                        value={member.matchingUsername}
                                        className='matching-username-input'
                                    />
                                    &nbsp;?
                                </span>
                            ) : null)
                    ) ?? '-'}</td>
                    <td
                        className='status'
                        title={`${LABEL_BY_ACTION[member.action]}: ${EXPLANATION_BY_ACTION[member.action]}`}
                    >
                        {LABEL_BY_ACTION[member.action]}
                    </td>
                    <td className='updates'>
                        {updates}
                    </td>
                </tr>);
            });
            content = (<>
                <table id='member-table'>
                    <tr>
                        <th className='username'>Benutzer</th>
                        <th className='user-info'>OLZ-Website-Konto</th>
                        <th
                            className='status'
                            title={`Dies sind Änderungen, die in der externen Benutzerverwaltung vorgenommen wurden. Diese wurden bereits angewendet.\n${PANIC_NOTICE}`}
                        >
                            Import-Status
                        </th>
                        <th
                            className='updates'
                            title='Dies sind Änderungen, die auf der Website vorgenommen wurden. Falls du diese in die externe Benutzerverwaltung übernehmen willst, klicke unten "Export", dann "CSV Download" und Importiere diese Datei dann in die externe Benutzerverwaltung.'
                        >
                            Updates für Export
                        </th>
                    </tr>
                    {memberRows}
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <div className='export-bar'>
                                <button
                                    id='export-button'
                                    className='btn btn-primary'
                                    onClick={onExport}
                                >
                                    Export
                                </button>
                                {exportCsv ? (
                                    <a
                                        id='csv-download'
                                        href={`${dataHref}temp/${exportCsv}`}
                                        download={'olz_mitglieder_update.csv'}>
                                        CSV Download
                                    </a>
                                ) : null}
                            </div>
                        </td>
                    </tr>
                </table>
            </>);
            break;
        }
        default:
            break;
    }

    return (<>
        <div className='olz-members'>
            {content}
        </div>
    </>);
};


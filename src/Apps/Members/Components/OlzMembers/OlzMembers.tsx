import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../../Api/client';
import {OlzApiRequests, OlzMemberInfo} from '../../../../Api/client/generated_olz_api_types';
import {OlzMultiFileField} from '../../../../Components/Upload/OlzMultiFileField/OlzMultiFileField';
import {dataHref} from '../../../../Utils/constants';
import {getResolverResult} from '../../../../Utils/formUtils';

import './OlzMembers.scss';

type ImportState = 'IDLE'|'UPLOADING'|'IMPORTING'|'IMPORTED';

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
    const [members, setMembers] = React.useState<OlzMemberInfo[]|null>(null);
    const [exportCsv, setExportCsv] = React.useState<string|null>(null);

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
                        <li>Unter "Zeilen:", wähle "Alle Zeilen"</li>
                        <li>Unter "Spalten:", wähle "Alle Spalten"</li>
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
        case 'IMPORTED':
            content = (<>
                <div className='export-bar'>
                    <button
                        className='btn btn-primary'
                        onClick={onExport}
                    >
                        Export
                    </button>
                    {exportCsv ? (
                        <a
                            href={`${dataHref}temp/${exportCsv}`}
                            download={'olz_mitglieder_update.csv'}>
                            CSV Download
                        </a>
                    ) : null}
                </div>
                <table>
                    <tr>
                        <th className='id'>Externe ID</th>
                        <th className='username'>Benuztername</th>
                        <th className='user-id'>OLZ-Benutzer-ID</th>
                        <th className='status'>Status</th>
                        <th className='updates'>Updates</th>
                    </tr>
                    {members?.map((member) => {
                        const updates = Object.keys(member.updates).map((key) =>
                            `${key}: "${member.updates[key].old}" => "${member.updates[key].new}"`);
                        return (<tr>
                            <td>{member.ident}</td>
                            <td>{member.username ?? '-'}</td>
                            <td>{member.userId ?? (member.matchingUsername ? <>➡️ <input type='text' readOnly value={member.matchingUsername} /> ?</> : null) ?? '-'}</td>
                            <td>{member.action}</td>
                            <td className='updates' title={updates.join('\n')}>{updates.join(', ')}</td>
                        </tr>);
                    })}
                </table>
            </>);
            break;
        default:
            break;
    }

    return (<>
        <div className='olz-members'>
            {content}
        </div>
    </>);
};


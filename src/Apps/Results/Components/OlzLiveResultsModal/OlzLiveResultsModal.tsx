import * as bootstrap from 'bootstrap';
import React from 'react';
import {olzApi} from '../../../../Api/client';
import {OlzApiRequests} from '../../../../Api/client/generated_olz_api_types';
import {olzConfirm} from '../../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';
import {initOlzEditModal, OlzEditModal} from '../../../../Components/Common/OlzEditModal/OlzEditModal';
import {readBase64} from '../../../../Utils/fileUtils';

import './OlzLiveResultsModal.scss';

interface OlzLiveResultsModalProps {
    data?: OlzApiRequests['updateResults'];
}

export const OlzLiveResultsModal = (props: OlzLiveResultsModalProps): React.ReactElement => {
    const [name, setName] = React.useState<string>(props.data?.file ?? '');
    const [fileHandle, setFileHandle] = React.useState<FileSystemFileHandle | null>(null);
    const [lastFileState, setLastFileState] = React.useState<string>('');

    const onChooseFile = async () => {
        if (!('showOpenFilePicker' in window)) {
            await olzConfirm('Live-Resultate sind in deinem Browser leider nicht möglich');
            return;
        }
        const showOpenFilePicker = (window as unknown as {showOpenFilePicker: (options: unknown) => Promise<FileSystemFileHandle[]>}).showOpenFilePicker;
        const pickerOptions = {
            types: [{description: 'IOF XML v3', accept: {'text/*': ['.xml']}}],
            excludeAcceptAllOption: true,
            multiple: false,
        };
        const [newFileHandle] = await showOpenFilePicker(pickerOptions);
        setFileHandle(newFileHandle);
    };

    React.useEffect(() => {
        if (!fileHandle) {
            return () => {};
        }
        const monitorFile = async () => {
            const file = await fileHandle.getFile();
            const newFileState = `${file.lastModified}-${file.size}`;
            if (newFileState === lastFileState) {
                return;
            }
            setLastFileState(newFileState);
            const content = await readBase64(file);
            const [err, response] = await olzApi.getResult('updateResults', {file: name, content});
            console.log(err, response);
        };
        monitorFile();
        const intervalHandle = window.setInterval(monitorFile, 1000);
        return () => {
            window.clearInterval(intervalHandle);
        };
    }, [fileHandle, lastFileState, name]);

    let liveUpload: React.ReactNode = (
        <button
            type='button'
            className='btn btn-secondary'
            onClick={onChooseFile}
        >
            Datei auswählen...
        </button>
    );
    if (fileHandle) {
        const match = /^([0-9]+)-([0-9]+)$/.exec(lastFileState);
        if (match) {
            const date = new Date(parseInt(match[1], 10));
            const size = Math.round(parseInt(match[2], 10) / 1024);
            liveUpload = (<>
                <b>Wird automatisch aktualisiert...</b>
                <div>Zuletzt aktualisiert: {date.toLocaleString('de-CH')}, Dateigrösse: {size} KB</div>
            </>);
        }
    }

    return (
        <OlzEditModal
            modalId='live-results-modal'
            dialogTitle='Live-Resultate'
            submitLabel={'Beenden'}
            status={{id: 'IDLE'}}
            onSubmit={(e) => {
                setFileHandle(null);
                setLastFileState('');
                e.stopPropagation();
                e.preventDefault();
                bootstrap.Modal.getInstance('#live-results-modal')?.hide();
            }}
        >
            <div className='mb-3'>
                Dateiname (muss auf .xml enden)
                <input
                    id='name-input'
                    className='form-control'
                    type='text'
                    name='name'
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                />
            </div>
            <div id='live-upload'>
                {liveUpload}
            </div>
        </OlzEditModal>
    );
};

export function initOlzLiveResultsModal(
    data?: OlzApiRequests['updateResults'],
): boolean {
    return initOlzEditModal('live-results-modal', () => (
        <OlzLiveResultsModal
            data={data}
        />
    ));
}

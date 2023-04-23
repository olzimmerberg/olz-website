import React from 'react';
import {olzApi} from '../../../../Api/client';

const COMMAND_REGEX = /^\s*([^\s]+)\s*(\s+(.+))?\s*$/;

export const OlzCommands = (): React.ReactElement => {
    const [output, setOutput] = React.useState<string>('Use `list` to list all available commands');
    const [commandAndArgv, setCommandAndArgv] = React.useState<string>('');

    const commandInput = React.useRef<HTMLInputElement>(null);

    const handleSubmit = React.useCallback(async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        const matches = COMMAND_REGEX.exec(commandAndArgv);
        const command = matches?.[1] ?? '';
        const argv = matches?.[3] ?? null;
        setOutput('Command running...');
        const result = await olzApi.call('executeCommand', {command, argv});
        setOutput(result.output);
        setCommandAndArgv('');
    }, [commandAndArgv]);

    return (
        <div>
            <pre
                tabIndex={-1}
                onKeyDown={(e) => {
                    if (e.ctrlKey || e.altKey || e.metaKey) {
                        return;
                    }
                    commandInput.current?.focus({preventScroll: true});
                }}
                onPaste={(e) => {
                    commandInput.current?.focus();
                    if (commandInput.current) {
                        const pastedText = e.clipboardData.getData('text');
                        setCommandAndArgv(pastedText);
                    }
                }}
            >
                {output}
            </pre>
            <form onSubmit={handleSubmit}>
                <input
                    type='text'
                    id='command-and-argv-input'
                    className='form-control form-control-sm'
                    ref={commandInput}
                    value={commandAndArgv}
                    onChange={(e) => {
                        const newCommandAndArgv = e.target.value;
                        setCommandAndArgv(newCommandAndArgv);
                    }}
                />
            </form>
        </div>
    );
};


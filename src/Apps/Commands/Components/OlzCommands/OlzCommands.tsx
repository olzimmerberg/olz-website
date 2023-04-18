import React from 'react';
import {olzApi} from '../../../../Api/client';

const COMMAND_REGEX = /^\s*([^\s]+)(\s+(.+)\s*)?$/;

export const OlzCommands = (): React.ReactElement => {
    const [output, setOutput] = React.useState<string>('Use `list` to list all available commands');
    const [commandAndArgv, setCommandAndArgv] = React.useState<string>('');

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
            <pre>
                {output}
            </pre>
            <form onSubmit={handleSubmit}>
                <input
                    type='text'
                    id='command-and-argv-input'
                    className='form-control form-control-sm'
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


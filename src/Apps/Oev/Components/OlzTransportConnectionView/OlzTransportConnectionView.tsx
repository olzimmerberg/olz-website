import React from 'react';
import olzTransitStations from '../../olz_transit_stations.json';
import {OlzTransportSuggestion, OlzTransportHalt, OlzTransportSection} from '../../../../Api/client/generated_olz_api_types';

import './OlzTransportConnectionView.scss';

const IS_OLZ_STATION: {[stationId: string]: true} = {};
for (const olzTransitStation of olzTransitStations) {
    IS_OLZ_STATION[olzTransitStation.id] = true;
}

const BACKGROUND_COLOR = 'rgb(220,220,220)';
const TRANSIT_COLOR = 'rgb(100,100,100)';
const CHANGE_COLOR = 'rgb(170,170,170)';

interface OlzTransportConnectionViewProps {
    suggestion: OlzTransportSuggestion;
}

export const OlzTransportConnectionView = (
    props: OlzTransportConnectionViewProps,
): React.ReactElement => {
    const suggestion = props.suggestion;
    return (<div>
        <br/>
        <br/>
        {getSvgFromInstructions(getPaintInstructions(suggestion))}
        <pre>
            {suggestion.debug}
        </pre>
        <br/>
        <b>Main connection</b>
        <table>
            {suggestion.mainConnection.sections.map((section) => (<>
                <tr className='departure'>
                    <td>{section.departure.stationName} ({section.departure.stationId})</td>
                    <td>{section.departure.time}</td>
                </tr>
                {section.passList
                    .filter((passStation) => IS_OLZ_STATION[passStation.stationId])
                    .map((passStation) => (
                        <tr className='halt'>
                            <td>{passStation.stationName} ({passStation.stationId})</td>
                            <td>{passStation.time}</td>
                        </tr>
                    ))
                }
                <tr className='arrival'>
                    <td>{section.arrival.stationName} ({section.arrival.stationId})</td>
                    <td>{section.arrival.time}</td>
                </tr>
            </>))}
        </table>
        <br/>
        <b>Side connections</b>
        <table>
            {suggestion.sideConnections.map((sideConnection) => (<>
                <br/>
                <div>Joining Station: {sideConnection.joiningStationId}</div>
                {sideConnection.connection.sections.map((section) => (<>
                    <tr className='departure'>
                        <td>{section.departure.stationName} ({section.departure.stationId})</td>
                        <td>{section.departure.time}</td>
                    </tr>
                    {section.passList
                        .filter((passStation) => IS_OLZ_STATION[passStation.stationId])
                        .map((passStation) => (
                            <tr className='halt'>
                                <td>{passStation.stationName} ({passStation.stationId})</td>
                                <td>{passStation.time}</td>
                            </tr>
                        ))
                    }
                    <tr className='arrival'>
                        <td>{section.arrival.stationName} ({section.arrival.stationId})</td>
                        <td>{section.arrival.time}</td>
                    </tr>
                </>))}
            </>))}
        </table>
        <br/>
    </div>);
};

export function getSvgFromInstructions(instructions: PaintInstruction[]): React.ReactElement {
    // const maxStationNameLength = instructions.reduce((prev, value) =>
    //     Math.max(prev, value.stationName.length), 0);
    const numConnections = instructions.reduce((prev, value) =>
        Math.max(prev, (value.connection ?? 0) + 1), 0);
    const heightPerInstruction = 20;
    const height = instructions.length * heightPerInstruction;
    const startIndexByConnection: {[connection: number]: number} = {};
    const endIndexByConnection: {[connection: number]: number} = {};
    return (
        <svg width={500} height={height}>
            <rect x={0} y={0} width={500} height={height} fill={BACKGROUND_COLOR} />
            {instructions.flatMap((instruction, index) => {
                if (instruction.connection === undefined) {
                    throw new Error('Instruction must have connection');
                }
                const output: React.ReactElement[] = [];
                if (instruction.role === 'departure') {
                    const endIndex = endIndexByConnection[instruction.connection];
                    startIndexByConnection[instruction.connection] = index;
                    if (endIndex !== undefined) {
                        output.push(
                            <line
                                x1={instruction.connection * 20 + 10}
                                y1={endIndex * 20 + 10}
                                x2={instruction.connection * 20 + 10}
                                y2={index * 20 + 10}
                                stroke={CHANGE_COLOR}
                                stroke-width={10}
                                stroke-linecap='round'
                            />,
                        );
                    }
                }
                if (instruction.role === 'arrival') {
                    const startIndex = startIndexByConnection[instruction.connection];
                    endIndexByConnection[instruction.connection] = index;
                    output.push(
                        <line
                            x1={instruction.connection * 20 + 10}
                            y1={startIndex * 20 + 10}
                            x2={instruction.connection * 20 + 10}
                            y2={index * 20 + 10}
                            stroke={TRANSIT_COLOR}
                            stroke-width={10}
                            stroke-linecap='round'
                        />,
                    );
                }
                output.push(...instruction.joinConnections.flatMap((joinConnection) => {
                    const endIndex = endIndexByConnection[joinConnection];
                    return [
                        <line
                            x1={joinConnection * 20 + 10}
                            y1={endIndex * 20 + 10}
                            x2={joinConnection * 20 + 10}
                            y2={index * 20 + 10}
                            stroke={CHANGE_COLOR}
                            stroke-width={10}
                            stroke-linecap='round'
                        />,
                        <line
                            x1={joinConnection * 20 + 10}
                            y1={index * 20 + 10}
                            x2={10}
                            y2={index * 20 + 10}
                            stroke={CHANGE_COLOR}
                            stroke-width={10}
                            stroke-linecap='round'
                        />,
                    ];
                }));
                return output;
            })}
            {instructions.map((instruction, index) => {
                if (instruction.connection === undefined) {
                    throw new Error('Instruction must have connection');
                }
                const isImportant = IS_OLZ_STATION[instruction.stationId] || instruction.role !== 'halt';
                if (isImportant) {
                    return (<>
                        <circle
                            cx={instruction.connection * 20 + 10}
                            cy={index * 20 + 10}
                            r={7}
                            stroke='rgb(0,0,0)'
                            stroke-width={2}
                            fill='rgb(255,255,255)'
                        />
                        <text
                            x={numConnections * 20 + 5}
                            y={index * 20 + 15}
                            fill='rgb(0,0,0)'
                            style={{
                                fontWeight: 'bold',
                                fontSize: 14,
                            }}
                        >
                            {`${instruction.time.substr(11, 5)} ${instruction.stationName}`}
                        </text>
                    </>);
                }
                return (<>
                    <circle
                        cx={instruction.connection * 20 + 10}
                        cy={index * 20 + 10}
                        r={3}
                        stroke='none'
                        stroke-width={0}
                        fill='rgb(255,255,255)'
                    />
                    <text
                        x={numConnections * 20 + 5}
                        y={index * 20 + 15}
                        fill='rgb(0,0,0)'
                        style={{
                            fontWeight: 'regular',
                            fontSize: 12,
                        }}
                    >
                        {`${instruction.time.substr(11, 5)} ${instruction.stationName}`}
                    </text>
                </>);
            })}
        </svg>
    );
}

type HaltRole = 'departure' | 'halt' | 'arrival' | 'skip';

interface PaintInstruction {
    connection?: number; // 0 = main connection, n = nth side connection
    stationId: string;
    stationName: string;
    time: string;
    role: HaltRole;
    joinConnections: number[];
}

export function getPaintInstructions(suggestion: OlzTransportSuggestion): PaintInstruction[] {
    const joinConnectionsByStationId: {[stationId: string]: number[]} = {};
    suggestion.sideConnections.map((sideConnection, sideConnectionIndex) => {
        const stationId = sideConnection.joiningStationId;
        const existingJoinConnections = joinConnectionsByStationId[stationId] ?? [];
        joinConnectionsByStationId[stationId] = [
            ...existingJoinConnections,
            sideConnectionIndex + 1,
        ];
    });
    const skippedOriginInstructions: PaintInstruction[] =
        suggestion.originInfo.filter((originInfo) => originInfo.isSkipped).map(
            (originInfo) => getPaintInstruction(undefined, originInfo.halt, 'skip'),
        );
    const mainConnectionInstructions: PaintInstruction[] =
        suggestion.mainConnection.sections
            .flatMap((section) => getPaintInstructionsFromSection(0, section))
            .reverse()
            .map((instruction) => {
                const joinConnections = joinConnectionsByStationId[instruction.stationId] ?? [];
                joinConnectionsByStationId[instruction.stationId] = [];
                return {
                    ...instruction,
                    joinConnections,
                };
            })
            .reverse();
    const sideConnectionInstructions: PaintInstruction[] =
        suggestion.sideConnections.flatMap((sideConnection, sideConnectionIndex) => {
            const joiningStationId = sideConnection.joiningStationId;
            let hasJoined = false;
            const sectionsToDraw = sideConnection.connection.sections.map((section) => {
                if (hasJoined) {
                    return null;
                }
                if (section.departure.stationId === joiningStationId) {
                    hasJoined = true;
                    return null;
                }
                if (section.arrival.stationId === joiningStationId) {
                    hasJoined = true;
                    return section;
                }
                const joiningIndex = section.passList.findIndex(
                    (halt) => halt.stationId === joiningStationId,
                );
                if (joiningIndex > -1) {
                    hasJoined = true;
                    return {
                        ...section,
                        passList: section.passList.slice(0, joiningIndex),
                        arrival: section.passList[joiningIndex],
                    };
                }
                return section;
            }).filter((section) => section !== null);
            return sectionsToDraw.flatMap(
                (section) => (section ? getPaintInstructionsFromSection(
                    sideConnectionIndex + 1, section,
                ) : []),
            );
        });
    const instructions = [
        ...skippedOriginInstructions,
        ...mainConnectionInstructions,
        ...sideConnectionInstructions,
    ];
    instructions.sort((instructionA, instructionB) =>
        (instructionA.time > instructionB.time ? 1 : -1));
    console.log(instructions);
    return instructions;
}

function getPaintInstructionsFromSection(
    connection: number,
    section: OlzTransportSection,
): PaintInstruction[] {
    if (section.isWalk) {
        return [];
    }
    return [
        getPaintInstruction(connection, section.departure, 'departure'),
        ...section.passList.map(
            (halt) => getPaintInstruction(connection, halt, 'halt'),
        ),
        getPaintInstruction(connection, section.arrival, 'arrival'),
    ];
}

function getPaintInstruction(
    connection: number | undefined,
    halt: OlzTransportHalt,
    role: HaltRole,
): PaintInstruction {
    return {
        connection,
        stationId: halt.stationId,
        stationName: halt.stationName,
        time: halt.time,
        role,
        joinConnections: [],
    };
}

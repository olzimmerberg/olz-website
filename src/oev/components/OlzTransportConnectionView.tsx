import React from 'react';
import olzTransitStations from '../../shared/olz_transit_stations.json';
import {OlzTransportConnectionSuggestion, OlzTransportHalt, OlzTransportSection} from '../../api/client/generated_olz_api_types';

import './OlzTransportConnectionView.scss';

const IS_OLZ_STATION: {[stationId: string]: true} = {};
for (const olzTransitStation of olzTransitStations) {
    IS_OLZ_STATION[olzTransitStation.id] = true;
}

interface OlzTransportConnectionViewProps {
    suggestion: OlzTransportConnectionSuggestion;
}

export const OlzTransportConnectionView = (
    props: OlzTransportConnectionViewProps,
) => {
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
        {suggestion.mainConnection.sections.map(section => (<>
            <tr className='departure'>
                <td>{section.departure.stationName} ({section.departure.stationId})</td>
                <td>{section.departure.time}</td>
            </tr>
            {section.passList
                .filter(passStation => IS_OLZ_STATION[passStation.stationId])
                .map(passStation => (
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
        {suggestion.sideConnections.map(sideConnection => (<>
            <br/>
            <div>Joining Station: {sideConnection.joiningStationId}</div>
            {sideConnection.connection.sections.map(section => (<>
                <tr className='departure'>
                    <td>{section.departure.stationName} ({section.departure.stationId})</td>
                    <td>{section.departure.time}</td>
                </tr>
                {section.passList
                    .filter(passStation => IS_OLZ_STATION[passStation.stationId])
                    .map(passStation => (
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
}

export function getSvgFromInstructions(instructions: PaintInstruction[]) {
    // const maxStationNameLength = instructions.reduce((prev, value) => 
    //     Math.max(prev, value.stationName.length), 0);
    const numConnections = instructions.reduce((prev, value) => 
        Math.max(prev, value.connection + 1), 0);
    const heightPerInstruction = 20;
    const height = instructions.length * heightPerInstruction;
    const startIndexByConnection: {[connection: number]: number} = {};
    return (
        <svg width={500} height={height}>
            <rect x={0} y={0} width={500} height={height} fill='rgb(200,200,200)' />
            {instructions.flatMap((instruction, index) => {
                if (instruction.role === 'departure') {
                    startIndexByConnection[instruction.connection] = index;
                    return [];
                }
                if (instruction.role === 'arrival') {
                    const startIndex = startIndexByConnection[instruction.connection];
                    return [
                        (
                            <line
                                x1={instruction.connection * 20 + 10}
                                y1={startIndex * 20 + 10}
                                x2={instruction.connection * 20 + 10}
                                y2={index * 20 + 10}
                                stroke='rgb(100,100,100)'
                                stroke-width={10}
                            />
                        ),
                    ];
                }
                return [];
            })}
            {instructions.map((instruction, index) => (<>
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
            </>))}
        </svg>
    );
}

type HaltRole = 'departure'|'halt'|'arrival';

interface PaintInstruction {
    connection: number; // 0 = main connection, n = nth side connection
    stationId: string;
    stationName: string;
    time: string;
    role: HaltRole;
}

export function getPaintInstructions(suggestion: OlzTransportConnectionSuggestion) {
    const mainConnectionInstructions: PaintInstruction[] = 
        suggestion.mainConnection.sections.flatMap(
            section => getPaintInstructionsFromSection(0, section));
    const sideConnectionInstructions: PaintInstruction[] = 
        suggestion.sideConnections.flatMap((sideConnection, sideConnectionIndex) => {
            const instructions = sideConnection.connection.sections.flatMap(
                section => getPaintInstructionsFromSection(
                    sideConnectionIndex + 1, section));
            const joiningStationIndex = instructions.findIndex(instruction => 
                instruction.stationId === sideConnection.joiningStationId);
            return [
                ...instructions.slice(0, joiningStationIndex),
                {
                    ...instructions[joiningStationIndex],
                    role: 'arrival',
                },
            ];
        });
    const instructions = [
        ...mainConnectionInstructions,
        ...sideConnectionInstructions,
    ];
    instructions.sort((instructionA, instructionB) => 
        instructionA.time > instructionB.time ? 1 : -1);
    return instructions;
}

function getPaintInstructionsFromSection(
    connection: number,
    section: OlzTransportSection,
): PaintInstruction[] {
    return [
        getPaintInstruction(connection, section.departure, 'departure'),
        ...section.passList.map(
            halt => getPaintInstruction(connection, halt, 'halt')),
        getPaintInstruction(connection, section.arrival, 'arrival'),
    ];
}

function getPaintInstruction(
    connection: number,
    halt: OlzTransportHalt,
    role: HaltRole,
) {
    return {
        connection,
        stationId: halt.stationId,
        stationName: halt.stationName,
        time: halt.time,
        role,
    };
}

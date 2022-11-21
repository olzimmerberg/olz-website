import {initOlzTransportConnectionSearch} from './Components/OlzOev/OlzOev';
export * from './Components/index';

export function loaded(): void {
    console.log('olzOev loaded');
    initOlzTransportConnectionSearch();
}

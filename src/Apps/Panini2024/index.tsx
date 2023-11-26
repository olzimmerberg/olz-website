import {initOlzPanini, initOlzPaniniMasks} from './Components/index';

export * from './Components/index';

export function loaded(): void {
    console.log('olzPanini2024 loaded');
    initOlzPanini();
    initOlzPaniniMasks();
}

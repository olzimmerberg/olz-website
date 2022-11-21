import {checkUpdate, loadUpdate} from './Components/OlzResults/OlzResults';
export * from './Components/index';

export function loaded(): void {
    console.log('olzResults loaded');
    window.setInterval(checkUpdate, 15000);
    loadUpdate();
}

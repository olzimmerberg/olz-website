import {getErrorOrThrow} from './generalUtils';

export type EventCallback<T> = (event: CustomEvent<T>) => void;

interface EventTypeDict {
    [typeName: string]: any;
}

type EventCallbacksDict<T extends EventTypeDict> = {
    [typeName in keyof T]?: EventCallback<T[typeName]>[]
};

export class EventTarget<T extends EventTypeDict> {
    private eventRegistry?: EventCallbacksDict<T>;

    addEventListener<K extends keyof T & string>(
        typeName: K,
        callback: EventCallback<T[K]>,
    ): void {
        const eventRegistry: EventCallbacksDict<T> = this.eventRegistry || {};
        const listeners = eventRegistry[typeName] || [];
        eventRegistry[typeName] = [
            ...listeners,
            callback,
        ];
        this.eventRegistry = eventRegistry;
    }

    removeEventListener<K extends keyof T & string>(
        typeName: K,
        callback: EventCallback<T[K]>,
    ): void {
        const eventRegistry: EventCallbacksDict<T> = this.eventRegistry || {};
        const listeners = eventRegistry[typeName] || [];
        eventRegistry[typeName] = listeners.filter(
            (listener: EventCallback<T[K]>) => listener !== callback,
        );
        this.eventRegistry = eventRegistry;
    }

    removeAllEventListeners(): void {
        this.eventRegistry = {};
    }

    dispatchEvent<K extends keyof T & string>(
        typeName: K,
        eventDetail: T[K],
    ): boolean {
        const event = new CustomEvent<T[K]>(typeName, {detail: eventDetail});
        const eventRegistry: EventCallbacksDict<T> = this.eventRegistry || {};
        const listeners = eventRegistry[typeName] || [];
        listeners.forEach((listener: EventCallback<T[K]>) => {
            try {
                listener(event);
            } catch (unk: unknown) {
                const err = getErrorOrThrow(unk);
                console.error(`Event Listener failed (${typeName}): ${err}`);
                console.info(err.stack);
            }
        });
        return !event.defaultPrevented;
    }
}

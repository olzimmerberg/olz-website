import {OlzApiEndpoint} from '../../../api/client';
import {olzDefaultFormSubmit, GetDataForRequestDict} from '../../../components/common/olz_default_form/olz_default_form';

export function olzEditableTextEdit(buttonElement: HTMLButtonElement): void {
    const containerDiv: HTMLElement|null|undefined = buttonElement.parentElement?.parentElement;
    if (!containerDiv) {
        return;
    }
    containerDiv.classList.add('is-editing');
}

export function olzEditableTextCancel(buttonElement: HTMLButtonElement): void {
    let currentElement: HTMLElement|null = buttonElement;
    while (currentElement && !currentElement.classList.contains('is-editing')) {
        currentElement = currentElement.parentElement;
    }
    if (!currentElement) {
        return;
    }
    currentElement.classList.remove('is-editing');
}

export function olzEditableTextSubmit<T extends OlzApiEndpoint>(
    endpoint: T,
    args: {[fieldId: string]: string|number|boolean},
    textArg: string,
    form: HTMLFormElement,
): boolean {
    const getDataForRequestDict: Partial<GetDataForRequestDict<T>> = {};

    const fieldIds = Object.keys(args);
    fieldIds.map((fieldId) => {
        const fieldValue = args[fieldId];
        // @ts-ignore
        getDataForRequestDict[fieldId] = () => fieldValue;
    });
    // @ts-ignore
    getDataForRequestDict[textArg] = (f) => f.text.value;

    olzDefaultFormSubmit(
        endpoint,
        getDataForRequestDict as GetDataForRequestDict<T>,
        form,
        handleResponse,
    );
    return false;
}

function handleResponse(): string|void {
    window.setTimeout(() => {
        // TODO: This could probably be done more smoothly!
        window.location.reload();
    }, 300);
    return 'Änderung erfolgreich.';
}

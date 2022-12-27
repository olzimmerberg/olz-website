import {OlzApiEndpoint} from '../../../../src/Api/client';
import {FieldResultOrDictThereof, OlzRequestFieldResult, olzDefaultFormSubmit, GetDataForRequestFunction, getFormField, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFieldResult, validFormData, invalidFormData} from '../OlzDefaultForm/OlzDefaultForm';

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

const handleResponse = () => {
    window.setTimeout(() => {
        // TODO: This could probably be done more smoothly!
        window.location.reload();
    }, 300);
    return 'Änderung erfolgreich.';
};

export function olzEditableTextSubmit<T extends OlzApiEndpoint>(
    endpoint: T,
    args: {[fieldId: string]: string|number|boolean},
    textArg: string,
    form: HTMLFormElement,
): boolean {
    const getDataForRequestFn: GetDataForRequestFunction<T> = (f: HTMLFormElement) => {
        const fieldResults: FieldResultOrDictThereof<any> = {
            [textArg]: getFormField(f, 'text'),
        };
        for (const argKey of Object.keys(args)) {
            fieldResults[argKey] = validFieldResult('', args[argKey]);
        }
        const castedFieldResults = fieldResults as OlzRequestFieldResult<T>;
        if (!isFieldResultOrDictThereofValid(castedFieldResults)) {
            return invalidFormData(getFieldResultOrDictThereofErrors(castedFieldResults));
        }
        return validFormData(getFieldResultOrDictThereofValue(castedFieldResults));
    };

    olzDefaultFormSubmit(
        endpoint,
        getDataForRequestFn,
        form,
        handleResponse,
    );
    return false;
}

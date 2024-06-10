import React from 'react';
import {FieldErrors, FieldValues, Path, Control, useController, UseControllerProps} from 'react-hook-form';
import {SimpleMdeReact} from 'react-simplemde-editor';
import EasyMDE from 'easymde';

import 'easymde/dist/easymde.min.css';

const MDE_TOOLBAR: Array<'|'|{
    name: string;
    action: string|((editor: EasyMDE) => void);
    className: string;
    title: string;
}> = [
    {
        name: 'bold',
        action: EasyMDE.toggleBold,
        className: 'fa fa-bold',
        title: 'Fettschrift',
    },
    {
        name: 'italic',
        action: EasyMDE.toggleItalic,
        className: 'fa fa-italic',
        title: 'Kursiv',
    },
    {
        name: 'heading',
        action: EasyMDE.toggleHeadingSmaller,
        className: 'fa fa-header fa-heading',
        title: 'Überschrift (mehrmals klicken; rotiert)',
    },
    '|',
    {
        name: 'quote',
        action: EasyMDE.toggleBlockquote,
        className: 'fa fa-quote-left',
        title: 'Zitat',
    },
    {
        name: 'code',
        action: EasyMDE.toggleCodeBlock,
        className: 'fa fa-code',
        title: 'Code',
    },
    {
        name: 'link',
        action: EasyMDE.drawLink,
        className: 'fa fa-link',
        title: 'Link',
    },
    '|',
    {
        name: 'unordered-list',
        action: EasyMDE.toggleUnorderedList,
        className: 'fa fa-list-ul',
        title: 'Punkte-Liste',
    },
    {
        name: 'ordered-list',
        action: EasyMDE.toggleOrderedList,
        className: 'fa fa-list-ol',
        title: 'Nummerierte Liste',
    },
    '|',
    {
        name: 'preview',
        action: EasyMDE.togglePreview,
        className: 'fa fa-eye no-disable',
        title: 'Vorschau ein/aus',
    },
    {
        name: 'side-by-side',
        action: EasyMDE.toggleSideBySide,
        className: 'fa fa-columns',
        title: 'Seitliche Live-Vorschau',
    },
    {
        name: 'fullscreen',
        action: EasyMDE.toggleFullScreen,
        className: 'fa fa-arrows-alt',
        title: 'Vollbildschirm ein/aus',
    },
    '|',
    {
        name: 'guide',
        action: 'https://www.markdownguide.org/basic-syntax/',
        className: 'fa fa-question-circle',
        title: 'Detailierte Anleitung anzeigen (Englisch!)',
    },
];

interface OlzMarkdownFieldProps<Values extends FieldValues, Name extends Path<Values>> {
    title?: React.ReactNode;
    name: Name;
    rules?: UseControllerProps<Values, Name>['rules'];
    errors?: FieldErrors<Values>;
    control: Control<Values, Name>;
    disabled?: boolean;
    placeholder?: string;
    autoComplete?: string;
}

export const OlzMarkdownField = <
    Values extends FieldValues,
    Name extends Path<Values>,
>(props: OlzMarkdownFieldProps<Values, Name>): React.ReactElement => {
    const errorMessage = props.errors?.[props.name]?.message;
    const errorClassName = errorMessage ? ' is-invalid' : '';
    const inputId = `${props.name}-input`;
    const className = `form-control${errorClassName}`;
    const labelComponent = <label htmlFor={inputId}>{props.title}</label>;
    const errorComponent = errorMessage && <p className='error'>{String(errorMessage)}</p>;

    const {field: {value, onChange}} = useController({
        name: props.name,
        control: props.control,
        rules: props.rules,
    });

    const handleChange = React.useCallback((newValue: string) => {
        onChange(newValue);
    }, []);
    React.useEffect(() => {
        setTimeout(() => {
            console.log('MANUAL UPDATE');
            onChange('');
            setTimeout(() => {
                console.log('MANUAL UPDATE', value);
                onChange(value);
            }, 1);
        }, 1);
    }, []);
    const options = React.useMemo(() => ({
        spellChecker: false,
        status: false,
        toolbar: MDE_TOOLBAR,
    }), []);

    if (props.disabled) {
        return (<>
            {labelComponent}
            <textarea
                value={value}
                className={className}
                id={inputId}
                disabled={props.disabled}
                placeholder={props.placeholder}
            />
            {errorComponent}
        </>);
    }

    return (<>
        {labelComponent}
        <SimpleMdeReact
            value={value}
            onChange={handleChange}
            options={options}
            textareaProps={{defaultValue: value}}
        />
        {errorComponent}
    </>);
};

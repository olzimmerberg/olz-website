<?php

function olz_profile_form($defaults) {
    $fallback_defaults = [
        'region' => 'ZH',
        'country_code' => 'CH',
    ];
    $defaults = array_merge($fallback_defaults, $defaults);
    // $esc_id = htmlentities(json_encode($user->getId()));
    $esc_first_name = htmlentities($defaults['first_name']);
    $esc_last_name = htmlentities($defaults['last_name']);
    $esc_username = htmlentities($defaults['username']);
    $esc_email = htmlentities($defaults['email']);
    $gender_default = array_search($defaults['gender'], ['M', 'F', 'O']) === false ? 'selected ' : '';
    $gender_male = $defaults['gender'] == 'M' ? 'selected ' : '';
    $gender_female = $defaults['gender'] == 'F' ? 'selected ' : '';
    $gender_other = $defaults['gender'] == 'O' ? 'selected ' : '';
    $esc_birthdate = htmlentities($defaults['birthdate']);
    $esc_street = htmlentities($defaults['street']);
    $esc_postal_code = htmlentities($defaults['postal_code']);
    $esc_city = htmlentities($defaults['city']);
    $esc_region = htmlentities($defaults['region']);
    $esc_country_code = htmlentities($defaults['country_code']);

    return <<<ZZZZZZZZZZ
<div class='row'>
    <div class='col form-group'>
        <label for='profile-first-name-input'>Vorname <span class='required-field-asterisk'>*</span></label>
        <input
            type='text'
            name='first-name'
            value='{$esc_first_name}'
            class='form-control'
            id='profile-first-name-input'
        />
    </div>
    <div class='col form-group'>
        <label for='profile-last-name-input'>Nachname <span class='required-field-asterisk'>*</span></label>
        <input
            type='text'
            name='last-name'
            value='{$esc_last_name}'
            class='form-control'
            id='profile-last-name-input'
        />
    </div>
</div>
<div class='row'>
    <div class='col form-group'>
        <label for='profile-username-input'>Benutzername <span class='required-field-asterisk'>*</span></label>
        <input
            type='text'
            name='username'
            value='{$esc_username}'
            class='form-control'
            id='profile-username-input'
        />
    </div>
    <div class='col change-password-cell'>
        <button
            type='button'
            class='btn btn-secondary'
            data-toggle='modal'
            data-target='#change-password-modal'
            id='change-password-button'
        >
            Passwort ändern
        </button>
    </div>
</div>
<div class='form-group'>
    <label for='profile-email-input'>E-Mail <span class='required-field-asterisk'>*</span></label>
    <input
        type='email'
        name='email'
        value='{$esc_email}'
        class='form-control'
        id='profile-email-input'
    />
</div>
<div class='row'>
    <div class='col form-group'>
        <label for='profile-gender-input'>Geschlecht</label>
        <select
            name='gender'
            class='form-control'
            id='profile-gender-input'
        >
            <option {$gender_default}value=''>unbekannt</option>
            <option {$gender_male}value='M'>männlich</option>
            <option {$gender_female}value='F'>weiblich</option>
            <option {$gender_other}value='O'>andere</option>
        </select>
    </div>
    <div class='col form-group'>
        <label for='profile-birthdate-input'>Geburtsdatum (Format: TT.MM.YYYY)</label>
        <input
            type='text'
            name='birthdate'
            value='{$esc_birthdate}'
            class='form-control'
            id='profile-birthdate-input'
        />
    </div>
</div>
<div class='form-group'>
    <label for='profile-street-input'>Adresse (mit Hausnummer)</label>
    <input
        type='text'
        name='street'
        value='{$esc_street}'
        class='form-control'
        id='profile-street-input'
    />
</div>
<div class='row'>
    <div class='col form-group'>
        <label for='profile-postal-code-input'>PLZ</label>
        <input
            type='text'
            name='postal-code'
            value='{$esc_postal_code}'
            class='form-control'
            id='profile-postal-code-input'
        />
    </div>
    <div class='col form-group'>
        <label for='profile-city-input'>Wohnort</label>
        <input
            type='text'
            name='city'
            value='{$esc_city}'
            class='form-control'
            id='profile-city-input'
        />
    </div>
</div>
<div class='row'>
    <div class='col form-group'>
        <label for='profile-region-input'>Region / Kanton</label>
        <input
            type='text'
            name='region'
            value='{$esc_region}'
            class='form-control'
            id='profile-region-input'
        />
    </div>
    <div class='col form-group'>
        <label for='profile-country-code-input'>Land</label>
        <input
            type='text'
            name='country-code'
            value='{$esc_country_code}'
            class='form-control'
            id='profile-country-code-input'
        />
    </div>
</div>
ZZZZZZZZZZ;
}

<?php

namespace Olz\Components\Auth\OlzProfileForm;

use Olz\Components\Common\OlzComponent;

class OlzProfileForm extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();

        $fallback_defaults = [
            'region' => 'ZH',
            'country_code' => 'CH',
        ];
        $defaults = array_merge($fallback_defaults, $args);

        // $esc_id = htmlentities(json_encode($user->getId()));
        $esc_first_name = htmlentities($defaults['first_name'] ?? '');
        $esc_last_name = htmlentities($defaults['last_name'] ?? '');
        $esc_username = htmlentities($defaults['username'] ?? '');
        $esc_email = htmlentities($defaults['email'] ?? '');
        $esc_phone = htmlentities($defaults['phone'] ?? '');
        $gender_default = array_search($defaults['gender'] ?? '', ['M', 'F', 'O']) === false ? 'selected ' : '';
        $gender_male = ($defaults['gender'] ?? '') == 'M' ? 'selected ' : '';
        $gender_female = ($defaults['gender'] ?? '') == 'F' ? 'selected ' : '';
        $gender_other = ($defaults['gender'] ?? '') == 'O' ? 'selected ' : '';
        $birthdate_formatted = ($defaults['birthdate'] ?? false) ? $defaults['birthdate']->format('d.m.Y') : '';
        $esc_birthdate = htmlentities($birthdate_formatted);
        $esc_street = htmlentities($defaults['street'] ?? '');
        $esc_postal_code = htmlentities($defaults['postal_code'] ?? '');
        $esc_city = htmlentities($defaults['city'] ?? '');
        $esc_region = htmlentities($defaults['region'] ?? '');
        $esc_country_code = htmlentities($defaults['country_code'] ?? '');
        $esc_si_card_number = htmlentities($defaults['si_card_number'] ?? '');
        $esc_solv_number = htmlentities($defaults['solv_number'] ?? '');

        $show_avatar = $defaults['show_avatar'] ?? false;
        $required_email = $defaults['required_email'] ?? true;
        $show_change_password = $defaults['show_change_password'] ?? false;
        $show_password = $defaults['show_password'] ?? false;
        $required_password = $defaults['required_password'] ?? false;
        $avatar_class = $show_avatar ? '' : ' hidden';
        $required_email_class = $required_email ? '' : ' hidden';
        $change_password_class = $show_change_password ? '' : ' hidden';
        $show_password_class = $show_password ? '' : ' hidden';
        $required_password_class = $required_password ? '' : ' hidden';

        $user = $this->authUtils()->getCurrentUser();
        $image_paths = $this->authUtils()->getUserAvatar($user);
        $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);

        return <<<ZZZZZZZZZZ
            <div class='olz-profile-form'>
                <div class='row{$avatar_class}'>
                    <div class='col mb-3 avatar-container'>
                        <img {$image_src_html} alt='avatar' id='avatar-img' />
                        <button
                            type='button'
                            class='btn btn-secondary'
                            onclick='return olz.olzProfileFormUpdateAvatar(this.form);'
                            id='update-user-avatar-button'
                        >
                            Bild anpassen
                        </button>
                        <button
                            type='button'
                            class='btn btn-secondary'
                            onclick='return olz.olzProfileFormRemoveAvatar(this.form);'
                            id='remove-user-avatar-button'
                        >
                            Bild löschen
                        </button>
                        <input
                            type='hidden'
                            name='avatar-id'
                            value=''
                            class='form-control'
                            id='profile-avatar-id-input'
                        />
                    </div>
                </div>
                <div class='row'>
                    <div class='col mb-3'>
                        <label for='profile-first-name-input'>Vorname <span class='required-field-asterisk'>*</span></label>
                        <input
                            type='text'
                            name='first-name'
                            value='{$esc_first_name}'
                            class='form-control'
                            id='profile-first-name-input'
                        />
                    </div>
                    <div class='col mb-3'>
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
                    <div class='col mb-3'>
                        <label for='profile-username-input'>Benutzername <span class='required-field-asterisk'>*</span></label>
                        <input
                            type='text'
                            name='username'
                            value='{$esc_username}'
                            class='form-control'
                            id='profile-username-input'
                            autocomplete='off'
                            onfocus='return olz.olzProfileFormOnUsernameFocus(this.form);'
                        />
                    </div>
                    <div class='col change-password-cell'>
                        <button
                            type='button'
                            class='btn btn-secondary{$change_password_class}'
                            onclick='return olz.initOlzChangePasswordModal()'
                            id='change-password-button'
                        >
                            Passwort ändern
                        </button>
                    </div>
                </div>
                <div class='row{$show_password_class}'>
                    <div class='col mb-3'>
                        <label for='profile-password-input'>Passwort <span class='required-field-asterisk{$required_password_class}'>*</span></label>
                        <input
                            type='password'
                            name='password'
                            class='form-control'
                            id='profile-password-input'
                            autocomplete='off'
                        />
                    </div>
                    <div class='col mb-3'>
                        <label for='profile-password-input'>Passwort wiederholen <span class='required-field-asterisk{$required_password_class}'>*</span></label>
                        <input
                            type='password'
                            name='password-repeat'
                            class='form-control'
                            id='profile-password-repeat-input'
                            autocomplete='off'
                        />
                    </div>
                </div>
                <div class='row'>
                    <div class='col mb-3'>
                        <label for='profile-email-input'>E-Mail <span class='required-field-asterisk{$required_email_class}'>*</span></label>
                        <input
                            type='text'
                            name='email'
                            value='{$esc_email}'
                            class='form-control'
                            id='profile-email-input'
                        />
                    </div>
                    <div class='col mb-3'>
                        <label for='profile-phone-input'>Telefonnummer (Format: +41XXXXXXXXX)</label>
                        <input
                            type='text'
                            name='phone'
                            value='{$esc_phone}'
                            class='form-control'
                            id='profile-phone-input'
                        />
                    </div>
                </div>
                <div class='row'>
                    <div class='col mb-3'>
                        <label for='profile-gender-input'>Geschlecht</label>
                        <select
                            name='gender'
                            class='form-control form-select'
                            id='profile-gender-input'
                        >
                            <option {$gender_default}value=''>unbekannt</option>
                            <option {$gender_male}value='M'>männlich</option>
                            <option {$gender_female}value='F'>weiblich</option>
                            <option {$gender_other}value='O'>andere</option>
                        </select>
                    </div>
                    <div class='col mb-3'>
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
                <div class='mb-3'>
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
                    <div class='col mb-3'>
                        <label for='profile-postal-code-input'>PLZ</label>
                        <input
                            type='text'
                            name='postal-code'
                            value='{$esc_postal_code}'
                            class='form-control'
                            id='profile-postal-code-input'
                        />
                    </div>
                    <div class='col mb-3'>
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
                    <div class='col mb-3'>
                        <label for='profile-region-input'>Region / Kanton (2-Buchstaben-Code, z.B. ZH)</label>
                        <input
                            type='text'
                            name='region'
                            value='{$esc_region}'
                            class='form-control'
                            id='profile-region-input'
                        />
                    </div>
                    <div class='col mb-3'>
                        <label for='profile-si-card-number-input'>Land (2-Buchstaben-Code, z.B. CH)</label>
                        <input
                            type='text'
                            name='country-code'
                            value='{$esc_country_code}'
                            class='form-control'
                            id='profile-country-code-input'
                        />
                    </div>
                </div>
                <div class='row'>
                    <div class='col mb-3'>
                        <label for='profile-si-card-number-input'>SI-Card-Nummer (Badge-Nummer)</label>
                        <input
                            type='text'
                            name='si-card-number'
                            value='{$esc_si_card_number}'
                            class='form-control'
                            id='profile-si-card-number-input'
                        />
                    </div>
                    <div class='col mb-3'>
                        <label for='solv-number-input'>SOLV-Nummer (siehe <a href='https://www.o-l.ch/cgi-bin/solvdb' target='_blank'>SOLV-DB</a>)</label>
                        <input
                            type='text'
                            name='solv-number'
                            value='{$esc_solv_number}'
                            class='form-control'
                            id='solv-number-input'
                        />
                    </div>
                </div>
            </div>
            ZZZZZZZZZZ;
    }
}

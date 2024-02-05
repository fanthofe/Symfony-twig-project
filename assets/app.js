/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './scss/variables.scss';
import './scss/utils.scss';
import './scss/registration.scss';
import './scss/reset_password.scss';
import './scss/component.scss';
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
import $ from 'jquery';

// Flatpickr 
import flatpickr from "../public/libs/flatpickr/flatpickr.min.js";
import { French } from "../public/libs/flatpickr/l10n/fr.js";



$(function() {

    // Flatpickr
    // Page Profil update
    flatpickr("#edit_profil_form_dateEntry", {
        altInput: true,
        altFormat: "j F Y",
        dateFormat: "Y-m-d H:i",
        locale: French
    });

    // Page Enterprise update
    flatpickr('#enterprise_form_creationDate', {
        altInput: true,
        altFormat: "j F Y",
        dateFormat: "Y-m-d H:i",
        locale: French
    });

    // Page Article update
    flatpickr('#article_form_date', {
        altInput: true,
        altFormat: "j F Y",
        dateFormat: "Y-m-d H:i",
        locale: French
    })
    
    // Article wisiwyg
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [[{ 'font': [] }, { 'size': [] }], ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }], [{ 'script': 'super' }, { 'script': 'sub' }], 
            [{ 'header': [false, 1, 2, 3, 4, 5, 6] }, 'blockquote', 'code-block'], 
            [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }], 
            ['direction', { 'align': [] }], ['link', 'image', 'video'], ['clean'], ['Resize', 'DisplaySize']],
        },
    })

    quill.on('text-change', function() {
        $('.quill').text(quill.root.innerHTML);
    });

    if($('.quill').val().length > 0){
        const delta = quill.clipboard.convert($('.quill').val())
        quill.updateContents(delta)
    }
});

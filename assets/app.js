import { registerReactControllerComponents } from '@symfony/ux-react';
import './bootstrap.js';

import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';


let table = new DataTable('#example', {
    responsive: true,
});

let table2 = new DataTable('#simple', {
    responsive: true,
    paging: false,
    searching: false,
});

let resultsTable = new DataTable('#results', {
    responsive: true,
    paging: false,
    searching: false,
});

registerReactControllerComponents(require.context('./react/controllers', true, /\.(j|t)sx?$/));

(() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
    let toastButton = document.querySelector('#dismiss');
    let toast = document.querySelector('#toast');

    toastButton.addEventListener('click', function () {
        toast.style.display = "none";
    });

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                form.classList.add('was-validated')
            }


        }, false)
    })
})()
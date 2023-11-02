//import { registerReactControllerComponents } from '@symfony/ux-react';
//import './bootstrap.js';

import jszip from 'jszip';
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-responsive-dt';
import 'datatables.net-rowgroup-dt';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

let tableOptions = {
    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
}


let table = new DataTable('#example', {
    responsive: true,
    "language": tableOptions
});

let pupilTable = new DataTable('#pupils', {
    responsive: true,
    "language": tableOptions,
    order: [
        [0, 'asc'],
        [1, 'asc'],
        [2, 'asc'],
    ],
    rowGroup: {
        dataSrc: [0]
    },
    columnDefs: [
        {
            targets: [0],
            visible: false
        }
    ]
});

let examTable = new DataTable('#exams', {
    responsive: true,
    "language": tableOptions,
    order: [
        [0, 'asc'],
        [1, 'asc']
    ],
    rowGroup: {
        dataSrc: [0, 1]
    },
    columnDefs: [
        {
            targets: [0,1],
            visible: false
        }
    ]
});

let table2 = new DataTable('#simple', {
    responsive: true,
    "language": tableOptions,
    paging: false,
    searching: false,
});

let resultsTable = new DataTable('#results', {
    responsive: true,
    "language": tableOptions,
    paging: false,
    searching: false,
});

//registerReactControllerComponents(require.context('./react/controllers', true, /\.(j|t)sx?$/));

(() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
    let toastButton = document.querySelector('#dismiss');
    let toast = document.querySelector('#toast');

    if(toast) {
        toastButton.addEventListener('click', function () {
            toast.style.display = "none";
        });
    }



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
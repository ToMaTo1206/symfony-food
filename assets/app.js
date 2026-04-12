import { startStimulusApp } from '@symfony/stimulus-bundle';
const app = startStimulusApp();

import 'bootstrap';

import Swal from 'sweetalert2';
window.Swal = Swal;

import 'bootstrap/dist/css/bootstrap.min.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import './styles/app.css';

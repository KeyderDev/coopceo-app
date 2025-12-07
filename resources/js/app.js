import axios from 'axios';

const token = localStorage.getItem('auth_token');
const coop = localStorage.getItem('coop_codigo');

if (token) axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
if (coop) axios.defaults.headers.common['X-Coop-Code'] = coop;

import './bootstrap';

import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { faUser, faHome, faSignOutAlt } from '@fortawesome/free-solid-svg-icons';

library.add(faUser, faHome, faSignOutAlt);

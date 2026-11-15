import '@fortawesome/fontawesome-free/css/all.min.css';

import Alpine from 'alpinejs';
import {
    Activity,
    ClipboardList,
    Image,
    LogIn,
    ShieldCheck,
    Stethoscope,
    createIcons,
} from 'lucide';

const icons = {
    Activity,
    ClipboardList,
    Image,
    LogIn,
    ShieldCheck,
    Stethoscope,
};

const renderIcons = () => createIcons({ icons });

window.Alpine = Alpine;
window.renderIcons = renderIcons;

Alpine.start();

document.addEventListener('DOMContentLoaded', renderIcons);

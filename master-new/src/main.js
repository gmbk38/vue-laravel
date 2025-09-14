import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import Navbar from './views/Navbar.vue';
import ToastNotification from './views/ToastNotification.vue';
import LoadingSpinner from './views/samples/LoadingSpinner.vue';

import DonutChart from './views/samples/DonutChart.vue';

import DevTable from './views/tables/DevTable.vue';
import UserDevTable from './views/tables/UserDevTable.vue';
import InvoicesTable from './views/tables/InvoicesTable.vue';
import ActsTable from './views/tables/ActsTable.vue';
import EditForm from './views/forms/DevEditForm.vue';
import GroupEditForm from './views/forms/DevGroupEditForm.vue';

import PayerCard from './views/PayerCard.vue';
import PayerForm from './views/forms/PayerForm.vue';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';
import './assets/css/main.css';

const app = createApp(App);
app.use(router);
app.component('navbar', Navbar);
app.component('notification', ToastNotification);
app.component('spinner', LoadingSpinner);

app.component('donut', DonutChart);

app.component('dev-table', DevTable);
app.component('user-dev-table', UserDevTable);
app.component('invoices-table', InvoicesTable);
app.component('acts-table', ActsTable);
app.component('edit-form', EditForm);
app.component('edit-group-form', GroupEditForm);
app.component('payer-card', PayerCard);
app.component('payer-form', PayerForm);
app.mount('#app');

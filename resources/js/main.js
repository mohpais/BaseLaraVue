import './bootstrap';
import App from '@/App.vue';

import { createApp } from 'vue';
const app = createApp(App);
app
    // .use(router)
    .mount('#app')

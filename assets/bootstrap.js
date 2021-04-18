import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import 'vuetify/src/styles/main.sass';
import '@mdi/font/css/materialdesignicons.css'

import VuetifyConfirm from 'vuetify-confirm'
import DatetimePicker from 'vuetify-datetime-picker'

Vue.use(Vuetify);
Vue.use(DatetimePicker)

const vuetify = new Vuetify({
    icons: {
        iconfont: "mdi"
    },
    theme: { dark: false }
});

Vue.use(VuetifyConfirm, { vuetify })

const opts = {
    icons: {
        iconfont: 'mdi'
    }
}

export default {vuetify: new Vuetify(opts)}
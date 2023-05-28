import { createApp } from "vue";
import { createPinia } from "pinia";

import staffProfile from "./ManageStaffProfile.vue";
import staffLinks from "./ManageStaffSocial.vue";

const staffProfileApp = createApp(staffProfile);
const staffLinksApp = createApp(staffLinks);
const store = createPinia();

staffProfileApp.use(store);
staffLinksApp.use(store);

createApp(staffProfile).mount("#admin_staff-edit-app");
createApp(staffLinks).mount("#social-links-app");

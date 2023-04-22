import { createApp } from "vue";
import App from "./App.vue";
import SocialLinks from "./ManageSocial.vue";

const app = createApp(App);
app.mount("#admin_staff-edit-app");

const socialLinks = createApp(SocialLinks);
socialLinks.mount("#social-links-app");

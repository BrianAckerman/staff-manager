import { createApp, reactive, inject } from "vue";
import App from "./App.vue";
import SocialLinks from "./ManageSocial.vue";

const app = createApp(App);
app.mount("#admin_staff-edit-app");

const socialLinks = createApp(SocialLinks, {
  socialLinks: state.socialLinks,
});
socialLinks.mount("#social-links-app");

const state = reactive({
  socialLinks: [],
});

app.provide("state", state);

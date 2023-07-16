import { createApp } from "vue";
import { createPinia } from "pinia";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import staffProfile from "./StaffProfile.vue";
import socialLinks from "./SocialLinks.vue";
import staffContactAssociations from "./StaffContactAssociations.vue";
import "../assets/core.css";

const store = createPinia();

const createAndMountApp = (component, mountPoint, store) => {
  const app = createApp(component);
  app.use(store);
  app.component("QuillEditor", QuillEditor);
  app.mount(mountPoint);
};

createAndMountApp(staffProfile, "#admin_staff-edit-app", store);
createAndMountApp(socialLinks, "#social-links-app", store);
createAndMountApp(
  staffContactAssociations,
  "#quick-contact-associations",
  store
);

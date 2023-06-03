import { createApp } from "vue";
import { createPinia } from "pinia";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import staffProfile from "./ManageStaffProfile.vue";
import staffLinks from "./ManageStaffSocial.vue";

const store = createPinia();

const createAndMountApp = (component, mountPoint, store) => {
  const app = createApp(component);
  app.use(store);
  app.component("QuillEditor", QuillEditor);
  app.mount(mountPoint);
};

createAndMountApp(staffProfile, "#admin_staff-edit-app", store);
createAndMountApp(staffLinks, "#social-links-app", store);

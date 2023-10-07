import { createApp } from "vue";
import { createPinia } from "pinia";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import Draggable from "vuedraggable";
import staffProfile from "./StaffProfile.vue";
import socialLinks from "./SocialLinks.vue";
import callsToAction from "./StaffCallsToAction.vue";
import staffContactAssociations from "./StaffContactAssociations.vue";
import "../assets/core.css";

// Import Quill and the htmlEditButton module
import Quill from "quill";
import htmlEditButton from "quill-html-edit-button";

// Register the htmlEditButton module with Quill
Quill.register("modules/htmlEditButton", htmlEditButton);

const store = createPinia();

const createAndMountApp = (component, mountPoint, store) => {
  const app = createApp(component);
  app.use(store);
  app.component("QuillEditor", QuillEditor);
  app.component("Draggable", Draggable);
  app.mount(mountPoint);
};

let postBody = document.getElementById("post-body-content");
if (postBody) {
  let staffhDiv = document.createElement("div");
  staffhDiv.id = "staffh_body";
  postBody.appendChild(staffhDiv);
}

//#admin_staff-edit-app

createAndMountApp(staffProfile, "#staffh_body", store);
createAndMountApp(socialLinks, "#staffh_social-links-app", store);
createAndMountApp(callsToAction, "#staffh_calls-to-action-app", store);
createAndMountApp(
  staffContactAssociations,
  "#quick-contact-associations",
  store
);

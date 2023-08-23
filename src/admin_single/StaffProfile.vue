<script>
import { mapWritableState } from "pinia";
import { useStaffStore } from "./stores/staffMember";
import { vMaska } from "maska";

export default {
  name: "admin_staff-edit",
  directives: { maska: vMaska },
  data: function () {
    return {
      title: "Staff Information",
      quillInstance: null,
    };
  },
  computed: {
    ...mapWritableState(useStaffStore, {
      staffInfo: "staffInfo",
    }),
  },
  mounted() {
    const staffStore = useStaffStore();
    // Retrieve the post content from the hidden input field
    const hidden = document.getElementById("staff_post_content");
    const postContentJSON = hidden?.value || "";

    // Parse the JSON string to a JavaScript object
    const postContent = postContentJSON ? JSON.parse(postContentJSON) : {};

    // Update the post content in the Pinia store
    staffStore.setPostContent(postContent);
  },
  watch: {
    staffInfo: {
      deep: true,
      handler(newValue) {
        let hidden = document.getElementById("staff_post_content");
        hidden.value = JSON.stringify(newValue);
        // console.log("Staff Info changed:", newValue);
      },
    },
  },
  methods: {
    handleEditorReady(editor) {
      this.quillInstance = editor;
    },
    openMediaPicker() {
      // Ensure the WordPress media library exists
      if (wp.media) {
        // Create the media frame.
        let frame = wp.media({
          title: "Select or Upload Media",
          button: {
            text: "Use this media",
          },
          multiple: false, // Set to true if you want to allow multiple file selection
        });

        // When an image is selected in the media frame...
        frame.on("select", () => {
          // Get media attachment details from the frame state
          let attachment = frame.state().get("selection").first().toJSON();

          // Let's assume you want to insert the image URL into Quill
          this.insertImageToQuill(attachment.url);

          console.log(attachment);
        });

        // Open the modal
        frame.open();
      }
    },
    insertImageToQuill(url) {
      if (this.quillInstance) {
        let range = this.quillInstance.getSelection();
        this.quillInstance.insertEmbed(range.index, "image", url);
      }
    },
  },
};
</script>

<template>
  <div class="staff-member-form">
    <div>
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" v-model="staffInfo.fullName" />
    </div>
    <div>
      <label for="job_title">Job Title:</label>
      <input type="text" id="job_title" v-model="staffInfo.jobTitle" />
    </div>
    <div>
      <label for="email">Email Address:</label>
      <input
        type="email"
        id="email"
        placeholder="yourname@example.com"
        v-model="staffInfo.email"
      />
    </div>
    <div>
      <label for="phone_number">Office Phone:</label>
      <input
        type="tel"
        id="phone_number"
        placeholder="(555) 123-4567"
        v-model="staffInfo.officePhone"
        v-maska
        data-maska="(###) ###-####"
      />
    </div>
    <div>
      <label for="phone_number">Cell Phone:</label>
      <input
        type="tel"
        id="phone_number"
        placeholder="(555) 123-4567"
        v-model="staffInfo.cellPhone"
        v-maska
        data-maska="(###) ###-####"
      />
    </div>
    <button @click.prevent="openMediaPicker">Add Media</button>
    <quill-editor
      @ready="handleEditorReady"
      theme="snow"
      v-model:content="staffInfo.about"
      contentType="html"
      :options="{
        theme: 'snow',
        modules: {
          toolbar: [
            [{ font: [] }, { size: [] }],
            ['bold', 'italic', 'underline', 'strike', 'blockquote'],
            [
              { list: 'ordered' },
              { list: 'bullet' },
              { indent: '-1' },
              { indent: '+1' },
            ],
            ['direction', { align: [] }],
            ['link', 'video'],
            [{ color: [] }, { background: [] }, 'html'], // custom button for raw html editing
          ],
          htmlEditButton: {},
        },
      }"
    ></quill-editor>
  </div>
</template>

<style scoped>
label {
  display: block;
  width: 100%;
  margin-bottom: 10px;
}

input,
textarea,
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  margin-bottom: 20px;
}

::placeholder {
  color: #b0b0b0;
  font-weight: 300;
  font-style: italic;
}

input[type="checkbox"] {
  width: auto;
  margin-right: 10px;
}

textarea {
  height: 150px;
}

button[type="submit"] {
  background-color: #4caf50;
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 20px;
}

button[type="submit"]:hover {
  background-color: #3e8e41;
}

.error-message {
  color: red;
  font-size: 14px;
  margin-bottom: 20px;
}
</style>

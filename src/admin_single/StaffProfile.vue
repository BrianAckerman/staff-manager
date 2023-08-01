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
    <quill-editor
      theme="snow"
      v-model:content="staffInfo.about"
      contentType="html"
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

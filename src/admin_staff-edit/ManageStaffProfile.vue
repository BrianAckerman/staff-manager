<script>
import { mapWritableState } from "pinia";
import { useStaffStore } from "./stores/staffMember";

export default {
  name: "admin_staff-edit",
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
    const postContentJSON = hidden.value;

    // Parse the JSON string to a JavaScript object
    const postContent = JSON.parse(postContentJSON);

    // Update the post content in the Pinia store
    staffStore.setPostContent(postContent);
  },
  watch: {
    staffInfo: {
      deep: true,
      handler(newValue, oldValue) {
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
    <ul>
      <li v-for="(link, index) in socialLinks" :key="index">
        <a :href="link.url">{{ link.type }}</a>
      </li>
    </ul>
    <div>
      <label for="first_name">First Name:</label>
      <input type="text" id="first_name" v-model="staffInfo.firstName" />
    </div>
    <div>
      <label for="last_name">Last Name:</label>
      <input type="text" id="last_name" v-model="staffInfo.lastName" />
    </div>
    <div>
      <label for="job_title">Job Title:</label>
      <input type="text" id="job_title" v-model="staffInfo.jobTitle" />
    </div>
    <div>
      <label for="email">Email Address:</label>
      <input type="email" id="email" v-model="staffInfo.emailAddress" />
    </div>
    <div>
      <label for="phone_number">Phone Number:</label>
      <input type="text" id="phone_number" v-model="staffInfo.phoneNumber" />
    </div>
    <div>
      <label for="bio">Bio/Description:</label>
      <textarea id="bio" v-model="staffInfo.bio"></textarea>
    </div>
    <div>
      <label for="department">Department/Division:</label>
      <input type="text" id="department" v-model="staffInfo.department" />
    </div>
    <div>
      <label for="hire_date">Hire Date:</label>
      <input type="date" id="hire_date" v-model="staffInfo.hireDate" />
    </div>
    <div>
      <label for="work_location">Work Location:</label>
      <input type="text" id="work_location" v-model="staffInfo.workLocation" />
    </div>
    <div>
      <label for="skills">Skills/Expertise:</label>
      <textarea id="skills" v-model="staffInfo.skills"></textarea>
    </div>
    <div>
      <label for="education">Education:</label>
      <textarea id="education" v-model="staffInfo.education"></textarea>
    </div>
    <div>
      <label for="awards">Awards/Recognition:</label>
      <textarea id="awards" v-model="staffInfo.awards"></textarea>
    </div>
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

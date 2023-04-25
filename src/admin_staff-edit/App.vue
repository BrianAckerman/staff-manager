<script>
import { inject, reactive } from "vue";

export default {
  name: "admin_staff-edit",
  setup() {
    const state = inject("state");

    return {
      socialLinks: state.socialLinks,
    };
  },
  data: function () {
    return {
      staffMemberData: {
        firstName: "",
        lastName: "",
        jobTitle: "",
        emailAddress: "",
        phoneNumber: "",
        bio: "",
        department: "",
        hireDate: "",
        workLocation: "",
        skills: "",
        education: "",
        awards: "",
        showInSidebar: false,
      },
    };
  },
};
</script>

<template>
  <div class="staff-member-form">
    <form @submit.prevent>
      <h3>Social Links:</h3>
      <ul>
        <li v-for="(link, index) in socialLinks" :key="index">
          <a :href="link.url">{{ link.type }}</a>
        </li>
      </ul>
      <div>
        <label for="first_name">First Name:</label>
        <input
          type="text"
          id="first_name"
          v-model="staffMemberData.firstName"
        />
      </div>
      <div>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" v-model="staffMemberData.lastName" />
      </div>
      <div>
        <label for="job_title">Job Title:</label>
        <input type="text" id="job_title" v-model="staffMemberData.jobTitle" />
      </div>
      <div>
        <label for="email">Email Address:</label>
        <input type="email" id="email" v-model="staffMemberData.email" />
      </div>
      <div>
        <label for="phone_number">Phone Number:</label>
        <input
          type="text"
          id="phone_number"
          v-model="staffMemberData.phoneNumber"
        />
      </div>
      <div>
        <label for="bio">Bio/Description:</label>
        <textarea id="bio" v-model="staffMemberData.bio"></textarea>
      </div>
      <div>
        <label for="department">Department/Division:</label>
        <input
          type="text"
          id="department"
          v-model="staffMemberData.department"
        />
      </div>
      <div>
        <label for="hire_date">Hire Date:</label>
        <input type="date" id="hire_date" v-model="staffMemberData.hireDate" />
      </div>
      <div>
        <label for="work_location">Work Location:</label>
        <input
          type="text"
          id="work_location"
          v-model="staffMemberData.workLocation"
        />
      </div>
      <div>
        <label for="skills">Skills/Expertise:</label>
        <textarea id="skills" v-model="staffMemberData.skills"></textarea>
      </div>
      <div>
        <label for="education">Education:</label>
        <textarea id="education" v-model="staffMemberData.education"></textarea>
      </div>
      <div>
        <label for="awards">Awards/Recognition:</label>
        <textarea id="awards" v-model="staffMemberData.awards"></textarea>
      </div>
    </form>
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

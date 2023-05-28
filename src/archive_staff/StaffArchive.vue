<script>
export default {
  data: function () {
    return {
      title: "Hello World",
      staffData: [],
    };
  },
  computed: {},
  methods: {},
  created: function () {
    this.staffData = window.staffData;
  },
};
</script>

<template>
  {{ title }}
  <ul class="two-col">
    <li v-for="(staff, index) in staffData" :key="index">
      <img
        v-if="staff.featured_image_url"
        :src="staff.featured_image_url"
        :alt="staff.firstName + ' ' + staff.lastName"
      />
      <ul>
        <h2 v-if="staff.firstName || staff.lastName">
          {{ staff.firstName }} {{ staff.lastName }}
        </h2>
        <li v-if="staff.jobtitle">{{ staff.jobTitle }}</li>
        <li v-if="staff.emailAddress">
          <a :href="'mailto:' + staff.emailAddress">{{ staff.emailAddress }}</a>
        </li>
        <li v-if="staff.phoneNumber">
          <a :href="'tel:' + staff.phoneNumber">{{ staff.phoneNumber }}</a>
        </li>
        <li v-if="staff.bio">{{ staff.bio }}</li>
        <li v-if="staff.department">{{ staff.department }}</li>
        <li v-if="staff.hireDate">{{ staff.hireDate }}</li>
        <li v-if="staff.workLocation">{{ staff.workLocation }}</li>
        <li v-if="staff.skills.length > 0">
          <ul>
            <li v-for="(skill, index) in staff.skills" :key="index">
              {{ skill }}
            </li>
          </ul>
        </li>
        <li v-if="staff.awards.length > 0">
          <ul>
            <li v-for="(award, index) in staff.awards" :key="index">
              {{ award }}
            </li>
          </ul>
        </li>
        <li v-if="staff.education.length > 0">
          <ul>
            <li v-for="(ed, index) in staff.education" :key="index">
              {{ ed }}
            </li>
          </ul>
        </li>
        <li v-if="staff.staffLinks.length > 0">
          <ul>
            <li v-for="(link, index) in staff.staffLinks" :key="index">
              <a :href="link.url" target="_blnak">{{ link.type }}</a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>
</template>

<style scoped>
img {
  max-width: 100%;
  width: 100%;
}
ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
li {
  border-radius: 10px;
  padding: 10px;
  border: solid 1px;
}
h2 {
  margin: 0;
  font-size: 20px;
}
/* Display Grid */
.two-col {
  display: grid;
  gap: 5%;
  grid-template-columns: 1fr 1fr;
}
</style>

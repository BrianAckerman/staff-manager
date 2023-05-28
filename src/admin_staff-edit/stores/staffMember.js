import { defineStore } from "pinia";

export const useStaffStore = defineStore({
  id: "staff",
  state: () => ({
    staffInfo: {
      firstName: "",
      lastName: "",
      jobTitle: "",
      emailAddress: "",
      phoneNumber: "",
      bio: "",
      department: "",
      hireDate: "",
      workLocation: "",
      skills: [],
      education: [],
      awards: [],
      showInSidebar: false,
      staffLinks: [],
    },
    post: null,
    loading: false,
    error: null,
  }),
  getters: {},
  actions: {
    setPostContent(content) {
      this.staffInfo = content;
    },
  },
});

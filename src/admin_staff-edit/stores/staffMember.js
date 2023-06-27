import { defineStore } from "pinia";

export const useStaffStore = defineStore({
  id: "staff",
  state: () => ({
    staffInfo: {
      fullName: "",
      jobTitle: "",
      email: "",
      officePhone: "",
      cellPhone: "",
      about: "",
      isQuickContact: false,
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

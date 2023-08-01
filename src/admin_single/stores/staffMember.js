import { defineStore } from "pinia";

export const useStaffStore = defineStore({
  id: "staff",
  state: () => ({
    staffInfo: {
      fullName: "",
      jobTitle: "",
      emailAddress: "",
      officePhone: "",
      cellPhone: "",
      about: "",
      isQuickContact: false,
      staffLinks: [],
      callsToAction: [],
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

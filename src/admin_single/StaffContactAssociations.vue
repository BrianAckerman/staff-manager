<template>
  <div>
    <TagInput
      @filteredTags=""
      :availableTags="availableTags"
      :selectedTags="selectedTags"
    />
    <label class="hidden" for="associated_contacts">{{ outputTags }}</label>
  </div>
</template>

<script>
import TagInput from "./components/TagInput.vue";

export default {
  name: "Associate Staff to Quick Contacts",
  components: {
    TagInput,
  },
  data() {
    return {
      availableTags: wpData.availableQuickContacts,
      selectedTags: wpData.associatedQuickContacts,
      data: wpData,
    };
  },
  computed: {
    outputTags() {
      const ids = this.selectedTags.map((tag) => parseInt(tag.id, 10));
      document.getElementById("associated_contacts").value =
        JSON.stringify(ids);
      return ids;
    },
  },
  mounted() {
    console.log("wpData:", this.data);
  },
  methods: {},
};
</script>

<style scoped>
label.hidden {
  display: none;
}
</style>

<template>
  <label for="">{{ this.label }}</label>
  <div class="tag-input">
    <div class="tag-list" v-if="tags">
      <span
        class="tag"
        v-for="item in this.temporaryContact.staff_members"
        :key="item.id"
        >{{ item.name }}
        <span v-if="item.status === 'draft'">&nbsp;(draft)</span>
        <span class="tag-close" @click="removeTag(item)">&times;</span>
      </span>
      <a
        v-if="this.temporaryContact.staff_members.length > 0"
        role="button"
        class="clear-button"
        @click="clearAllTags"
        >Clear</a
      >
    </div>
    <input
      class="tag-input-field"
      type="text"
      placeholder="Search..."
      v-model="inputValue"
      @keydown.enter.prevent="addTag"
      @input="displayDropdown"
      @click="toggleDropdown"
      @blur="hideDropdown"
    />
    <ul class="tag-dropdown" v-show="showDropdown">
      <li v-for="tag in filteredTags" :key="tag.id" @click="addTag(tag)">
        {{ tag.name }} <span v-if="tag.status === 'draft'">(draft)</span>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
    },
    // Temporary contact for editing
    temporaryContact: {
      type: Object,
      required: true,
    },
  },
  emits: ["add-tag", "remove-tag", "clear-all"],
  data() {
    return {
      inputValue: "",
      availableTagsList: [],
      showDropdown: false,
    };
  },
  computed: {
    tags() {
      return this.temporaryContact.staff_members;
    },
    filteredTags() {
      if (!this.availableTagsList) {
        return []; // Return the complete available tags list if tags are not selected
      }

      return this.availableTagsList.filter((tag) => {
        const tagName = tag.name.toLowerCase();
        const input = this.inputValue.toLowerCase();
        return tagName.includes(input) && !this.isTagSelected(tag.id);
      });
    },
  },
  mounted() {
    // Get the list of published staff for the tag list options
    this.fetchTags();
  },
  methods: {
    async fetchTags() {
      if (!wpData.canEditPosts) {
        console.log("User does not have edit_posts capability");
        return;
      }

      try {
        const response = await fetch(
          "/wp-json/wp/v2/staff-members?status=any",
          {
            headers: {
              "X-WP-Nonce": wpData.nonce,
            },
          }
        );
        const jsonData = await response.json();
        jsonData.forEach((item) => {
          const id = item.id;
          const title = item.title.rendered;
          const status = item.status;
          let newItem = {
            id: id,
            name: title,
            status: status,
          };
          this.availableTagsList.push(newItem);
        });
      } catch (error) {
        console.log(error);
      }
    },
    addTag(staff) {
      this.showDropdown = true;
      this.inputValue = "";
      this.showDropdown = false;
      this.$emit("add-tag", staff); // Emit the event to the parent component
    },
    removeTag(tag) {
      this.$emit("remove-tag", tag.id); // Emit the "remove-staff" event
      if (this.temporaryContact.staff_members.length === 0) {
        this.showDropdown = true;
      }
    },
    isTagSelected(tagId) {
      if (this.temporaryContact.staff_members) {
        return this.temporaryContact.staff_members.some(
          (tag) => parseInt(tag.id, 10) === tagId
        );
      }
    },
    clearAllTags() {
      this.inputValue = "";
      this.showDropdown = true;
      document.querySelector(".tag-input-field").focus();
      this.$emit("clear-all"); // Emit the "clear-all" event to the parent component
    },
    displayDropdown() {
      this.showDropdown = true;
    },
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },
    hideDropdown() {
      setTimeout(() => {
        this.showDropdown = false;
      }, 200);
    },
  },
};
</script>

<style>
.tag-input {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 5px;
}

.tag-close {
  border: solid 1px #fff;
  background: #fff;
  border-radius: 20px;
  cursor: pointer;
  display: inline-block;
  height: 20px;
  margin-left: 5px;
  text-align: center;
  width: 20px;
}

.clear-button {
  font-size: 0.75rem;
  color: blue;
  text-decoration: underline;
  width: 100%;
  padding: 0.5rem 0.5rem 0 0.5rem;
  cursor: pointer;
}

.tag-dropdown {
  cursor: pointer;
  list-style: none;
  margin: 0;
  padding: 0;
}

.tag-dropdown li {
  padding: 0.25rem;
}

.tag-dropdown li:hover {
  background: #f5f5f5;
}

.tag-input-field {
  border: solid #ccc 1px;
  border-radius: 4px;
  box-sizing: border-box;
  margin: 5px 0;
  padding: 0.5rem;
  width: 100%;
}

.tag-list {
  align-items: center;
  display: flex;
  flex-wrap: wrap;
  padding-bottom: 0.5rem;
}

.tag {
  align-items: center;
  background-color: #f2f2f2;
  border-radius: 4px;
  display: flex;
  margin: 3px;
  padding: 3px 8px;
}
</style>

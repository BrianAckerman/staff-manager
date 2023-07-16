<template>
  <label for="">{{ this.label }}</label>
  <div class="tag-input">
    <div class="tag-list" v-if="selectedTags">
      <span id="" class="tag" v-for="tag in selectedTags" :key="tag.id"
        >{{ tag.name }}
        <span v-if="tag.status === '0'">(inactive)</span>
        <span class="tag-close" @click="removeTag(tag)">&times;</span>
      </span>
      <a
        v-if="this.selectedTags.length > 0"
        role="button"
        class="clear-button"
        @click="clearAllTags"
        >Clear</a
      >
    </div>
    <input
      id="tag-input-field"
      type="text"
      placeholder="Search..."
      v-model="inputValue"
      @keydown.enter.prevent="addTag"
      @input="filterTags"
      @click="toggleDropdown"
      @blur="hideDropdown"
    />
    <ul class="tag-dropdown" v-show="showDropdown">
      <li v-for="tag in filteredTags" :key="tag.id" @click="addTag(tag)">
        {{ tag.name }} <span v-if="tag.status === '0'">(inactive)</span>
      </li>
      <li v-if="filteredTags.length === 0">
        <p>
          <small
            >No more quick contacts available.<br />
            Go to
            <a
              href="/wp-admin/admin.php?page=staff_manager_quick_contacts"
              target="_blank"
              >Quick Contacts</a
            >
            and create more.</small
          >
        </p>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    availableTags: {
      type: Array,
      required: true,
    },
    selectedTags: {
      type: Array,
      required: true,
    },
  },
  emits: ["filtered-tags"],
  data() {
    return {
      inputValue: "",
      showDropdown: false,
    };
  },
  computed: {
    filteredTags() {
      const input = this.inputValue.toLowerCase();
      return this.availableTags.filter((tag) => {
        const tagName = tag.name.toLowerCase();
        const isSelected = this.selectedTags.some(
          (selectedTag) => parseInt(selectedTag.id, 10) === parseInt(tag.id, 10)
        );
        return tagName.includes(input) && !isSelected;
      });
    },
  },
  methods: {
    addTag(tag) {
      this.showDropdown = true;
      this.inputValue = "";
      this.selectedTags.push(tag);
    },
    removeTag(tag) {
      const index = this.selectedTags.findIndex(
        (selectedTag) => selectedTag.id === tag.id
      );
      if (index !== -1) {
        this.selectedTags.splice(index, 1)[0]; // Remove the tag from selectedTags and get the removed item
      }
    },
    clearAllTags() {
      this.inputValue = "";
      this.showDropdown = true;
      this.selectedTags.splice(0); // remove all the items in the array
      document.getElementById("tag-input-field").focus();
    },
    filterTags() {
      this.filteredTags = this.availableTags.filter((tag) =>
        tag.name.toLowerCase().includes(this.inputValue.toLowerCase())
      );
      console.log(this.filteredTags);
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
.tag-input * {
  box-sizing: border-box;
}
.tag-input {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 5px;
  position: relative;
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
  padding: 3px;
  position: absolute;
  z-index: 1;
  background: #fff;
  width: 100%;
  border: solid 1px #ddd;
  border-top: 0 none;
  box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.05);
  max-height: 10em;
  overflow-y: scroll;
}

.tag-dropdown li {
  padding: 0.25rem;
}

.tag-dropdown li:hover {
  background: #f5f5f5;
}

.tag-dropdown p {
  padding: 5px;
  margin: 0;
}

#tag-input-field {
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

.tags {
  margin-top: 10px;
}

.tag {
  display: inline-block;
  background-color: #e2e8f0;
  color: #4a5568;
  padding: 0.25rem 0.5rem;
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
  border-radius: 0.25rem;
  cursor: pointer;
  align-items: center;
  background-color: #f2f2f2;
  border-radius: 4px;
  display: flex;
  margin: 3px;
  padding: 3px 8px;
}
</style>

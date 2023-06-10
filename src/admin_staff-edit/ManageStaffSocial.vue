<template>
  <div v-if="!view || staffInfo.staffLinks.length < 1" class="msmp_staff-links">
    <form @submit.prevent="save">
      <div class="msmp_form-group">
        <label for="link-type">Network or Website:</label>
        <select
          name="link-type"
          v-model="activeLink.type"
          required
          class="msmp_form-control"
        >
          <option value="">- Select -</option>
          <option value="Facebook">Facebook</option>
          <option value="Instagram">Instagram</option>
          <option value="Twitter">Twitter</option>
          <option value="LinkedIn">LinkedIn</option>
          <option value="YouTube">YouTube</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="msmp_form-group">
        <label for="link-url">Link:</label>
        <input
          type="url"
          name="link-url"
          required
          v-model="activeLink.url"
          class="msmp_form-control"
        />
      </div>
      <div>
        <button type="submit" class="msmp_btn msmp_btn-primary">Save</button>
        <button
          @click="cancel"
          type="reset"
          class="msmp_btn msmp_btn-secondary"
          v-if="staffInfo.staffLinks.length > 0"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
  <div v-else class="msmp_staff-links">
    <ul class="msmp_social-links-list">
      <li
        v-for="(link, index) in staffInfo.staffLinks"
        :key="index"
        class="msmp_social-link-item"
      >
        <span
          :class="
            typeof link.type !== 'undefined' ? link.type.toLowerCase() : ''
          "
          >{{ link.type }} {{ link.url }}</span
        >
        <button @click.prevent="edit(index)" class="msmp_btn msmp_btn-link">
          Edit
        </button>
        <button @click.prevent="remove(index)" class="msmp_btn msmp_btn-link">
          Delete
        </button>
      </li>
    </ul>
    <button class="msmp_btn msmp_btn-secondary" @click.prevent="this.new">
      New
    </button>
  </div>
</template>

<script>
import { mapWritableState } from "pinia";
import { useStaffStore } from "./stores/staffMember";

export default {
  data: function () {
    return {
      view: null,
      activeIndex: null,
      activeLink: {},
    };
  },
  computed: {
    ...mapWritableState(useStaffStore, {
      staffInfo: "staffInfo",
    }),
  },
  created() {
    if (this.staffInfo.staffLinks.length > 0) {
      this.view = true;
    } else {
      this.view = false;
    }
  },
  methods: {
    new() {
      this.activeLink = {
        type: "",
        url: "",
      };
      this.activeIndex = this.staffInfo.staffLinks.length + 1;
      this.view = false;
    },
    edit(index) {
      this.activeIndex = index;
      this.activeLink = this.staffInfo.staffLinks[index];
      this.view = false;
    },
    save(event) {
      if (
        this.staffInfo.staffLinks[this.activeIndex] &&
        this.activeIndex !== null
      ) {
        // Update an existing record
        this.staffInfo.staffLinks[this.activeIndex].type = this.activeLink.type;
        this.staffInfo.staffLinks[this.activeIndex].url = this.activeLink.url;
        this.view = true;
      } else {
        // Push a new record
        this.staffInfo.staffLinks.push({
          type: this.activeLink.type,
          url: this.activeLink.url,
        });
        this.view = true;
      }
    },
    remove(index) {
      this.activeLink = {};
      if (this.staffInfo.staffLinks.length > 0) {
        this.staffInfo.staffLinks.splice(index, 1);
      } else {
        this.staffInfo.staffLinks = [];
        this.view = false;
      }
    },
    cancel() {
      this.view = true;
    },
  },
};
</script>

<style scoped>
.msmp_staff-links {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.msmp_social-links-list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.msmp_social-link-item {
  margin: 0;
  padding: 5px 0;
  display: flex;
  justify-content: space-between;
}

.msmp_social-link-item span {
  flex-grow: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.msmp_btn-link span {
  font-size: 16px;
  color: #9a9a9a;
}

.msmp_btn-link span:hover {
  color: #343434;
}

.msmp_btn {
  cursor: pointer;
}

.msmp_btn-link {
  padding: 0 0 0 0.5em;
  background: none;
  border: 0 none;
  text-decoration: underline;
}

.msmp_btn-link:hover {
  color: #135e96;
}

.msmp_form-group label {
  display: block;
  margin-top: 1em;
  margin-bottom: 0.25em;
  line-height: 1.25;
}

.msmp_form-control {
  width: 100%;
}

.msmp_btn-primary {
  display: inline-block;
  text-decoration: none;
  font-size: 13px;
  line-height: 2.15384615;
  min-height: 30px;
  margin: 1em 0.5em 0 0;
  padding: 0 10px;
  cursor: pointer;
  border-width: 1px;
  border-style: solid;
  -webkit-appearance: none;
  border-radius: 3px;
  white-space: nowrap;
  box-sizing: border-box;
  color: #f6f7f7;
  border-color: #2271b1;
  background: #2271b1;
}

.msmp_btn-primary:hover {
  background: #135e96;
  border-color: #135e96;
  color: #fff;
}

.msmp_btn-secondary {
  display: inline-block;
  text-decoration: none;
  font-size: 13px;
  line-height: 2.15384615;
  min-height: 30px;
  margin: 0;
  padding: 0 10px;
  cursor: pointer;
  border-width: 1px;
  border-style: solid;
  -webkit-appearance: none;
  border-radius: 3px;
  white-space: nowrap;
  box-sizing: border-box;
  color: #2271b1;
  border-color: #2271b1;
  background: #f6f7f7;
}

.msmp_btn-secondary:hover {
  background: #f0f0f1;
  border-color: #0a4b78;
  color: #0a4b78;
}
</style>

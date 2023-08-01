<template>
  <div v-if="!view || !hasStaffLinks" class="staffh_staff-links">
    <form @submit.prevent="save">
      <div class="staffh_form-group">
        <label for="link-type">Network or Website:</label>
        <select
          name="link-type"
          v-model="activeLink.type"
          required
          class="staffh_form-control"
        >
          <option value="">- Select -</option>
          <option value="Facebook">Facebook</option>
          <option value="Instagram">Instagram</option>
          <option value="𝕏">𝕏</option>
          <option value="LinkedIn">LinkedIn</option>
          <option value="YouTube">YouTube</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="staffh_form-group">
        <label for="link-url">Link:</label>
        <input
          type="url"
          name="link-url"
          required
          v-model="activeLink.url"
          class="staffh_form-control"
        />
      </div>
      <div>
        <button type="submit" class="staffh_btn staffh_btn-primary">
          Save
        </button>
        <button
          @click="cancel"
          type="reset"
          class="staffh_btn staffh_btn-secondary"
          v-if="hasStaffLinks"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
  <div v-else class="staffh_staff-links">
    <ul class="staffh_social-links-list">
      <li
        v-for="(link, index) in staffInfo.staffLinks"
        :key="index"
        class="staffh_social-link-item"
        :class="typeof link.type !== 'undefined' ? link.type.toLowerCase() : ''"
        :title="link.url"
      >
        <span class="icon">
          <img
            :src="iconBaseUrl + '/' + returnIconFileName(link.type)"
            :alt="link.type"
          />
        </span>
        <span>{{ link.url }}</span>
        <button
          role="button"
          title="Edit"
          @click.prevent="edit(index)"
          class="staffh_btn staffh_btn-link"
        >
          Edit
        </button>
        <button
          role="button"
          title="Delete"
          @click.prevent="remove(index)"
          class="staffh_btn staffh_btn-link"
        >
          Delete
        </button>
      </li>
    </ul>
    <button class="staffh_btn staffh_btn-secondary" @click.prevent="this.new">
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
      iconBaseUrl: wpData.staffh_img_url,
    };
  },
  computed: {
    ...mapWritableState(useStaffStore, {
      staffInfo: "staffInfo",
    }),
    hasStaffLinks() {
      if (this.staffInfo && this.staffInfo.staffLinks) {
        return this.staffInfo.staffLinks.length > 0;
      }
      return false;
    },
  },
  created() {
    this.view = Boolean(this.staffInfo?.staffLinks?.length);
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
    save() {
      if (!this.staffInfo.staffLinks) {
        this.staffInfo.staffLinks = [];
      }
      if (
        this.staffInfo.staffLinks[this.activeIndex] &&
        this.activeIndex !== null
      ) {
        // Update an existing record
        this.staffInfo.staffLinks[this.activeIndex].type = this.activeLink.type;
        this.staffInfo.staffLinks[this.activeIndex].url = this.activeLink.url;
      } else {
        // Push a new record
        this.staffInfo.staffLinks.push({
          type: this.activeLink.type,
          url: this.activeLink.url,
        });
      }
      this.view = true;
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
    returnIconFileName(linkType) {
      const iconMap = {
        facebook: "facebook_logo.svg",
        "𝕏": "twitx_logo.svg",
        instagram: "instagram_logo.svg",
        youtube: "youtube_logo.svg",
        linkedin: "linkedin_logo.svg",
      };

      return iconMap[linkType.toLowerCase()] || "other-link_logo.svg"; // default_icon.svg is used when no match is found
    },
  },
};
</script>

<style scoped>
.staffh_staff-links {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.staffh_social-links-list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.staffh_social-link-item {
  margin: 0 0 2px;
  padding: 5px 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 10px;
  border-bottom: solid 1px #ccc;
}

.staffh_social-link-item span {
  flex-grow: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.staffh_social-link-item span.icon {
  width: 22px;
  margin-right: 8px;
  flex-grow: 0;
}

.staffh_social-link-item span.icon img {
  max-width: 100%;
  vertical-align: middle;
}

.staffh_btn-link span {
  font-size: 16px;
  color: #9a9a9a;
}

.staffh_btn-link span:hover {
  color: #343434;
}

.staffh_btn {
  cursor: pointer;
}

.staffh_btn-link {
  padding: 0 0 0 0.5em;
  background: none;
  border: 0 none;
  text-decoration: underline;
}

.staffh_btn-link:hover {
  color: #135e96;
}

.staffh_form-group label {
  display: block;
  margin-top: 1em;
  margin-bottom: 0.25em;
  line-height: 1.25;
}

.staffh_form-control {
  width: 100%;
}

.staffh_btn-primary {
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

.staffh_btn-primary:hover {
  background: #135e96;
  border-color: #135e96;
  color: #fff;
}

.staffh_btn-secondary {
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

.staffh_btn-secondary:hover {
  background: #f0f0f1;
  border-color: #0a4b78;
  color: #0a4b78;
}
</style>

<template>
  <div v-if="!view || !hasCallsToAction" class="staffh_calls_to_action-links">
    <form @submit.prevent="save">
      <div class="staffh_form-group">
        <label for="link-type">Style:</label>
        <select
          name="link-type"
          v-model="activeButton.type"
          required
          class="staffh_form-control"
        >
          <option value="">- Select -</option>
          <option value="primary">Primary</option>
          <option value="secondary">Secondary</option>
          <option value="tertiary">Tertiary</option>
        </select>
      </div>
      <div class="staffh_form-group">
        <label for="button-label">Button Text:</label>
        <input
          type="text"
          name="button-label"
          required
          v-model="activeButton.label"
          class="staffh_form-control"
        />
      </div>
      <div class="staffh_form-group">
        <label for="link-url">Link:</label>
        <input
          type="url"
          name="link-url"
          required
          v-model="activeButton.url"
          class="staffh_form-control"
        />
      </div>
      <div>
        <button type="submit" class="staffh_btn staffh_btn-primary">Add</button>
        <button
          @click="cancel"
          type="reset"
          class="staffh_btn staffh_btn-secondary"
          v-if="hasCallsToAction"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
  <div v-else class="staffh_calls_to_action">
    <Draggable
      v-model="staffInfo.callsToAction"
      class="staffh_calls_to_action-list"
      group="callsToAction"
      :animation="200"
    >
      <template #item="{ element, index }">
        <li
          :key="index"
          class="staffh_calls_to_action-item"
          :class="
            typeof element.type !== 'undefined'
              ? element.type.toLowerCase()
              : ''
          "
          :title="element.url"
        >
          <span class="grip-dots">
            <img
              :src="iconBaseUrl + '/fontawesome/grip-dots-vertical.svg'"
              alt=""
            />
          </span>
          <span class="cta-label">{{ element.label }}</span>
          <span>
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
          </span>
        </li>
      </template>
    </Draggable>

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
      activeButton: {},
      iconBaseUrl: wpData.staffh_img_url,
    };
  },
  computed: {
    ...mapWritableState(useStaffStore, {
      staffInfo: "staffInfo",
    }),
    hasCallsToAction() {
      if (this.staffInfo && this.staffInfo.callsToAction) {
        return this.staffInfo.callsToAction.length > 0;
      }
      return false;
    },
  },
  created() {
    this.view = Boolean(this.staffInfo?.callsToAction?.length);
  },
  methods: {
    new() {
      this.activeButton = {
        type: "",
        url: "",
        label: "",
      };
      this.activeIndex = this.staffInfo.callsToAction.length + 1;
      this.view = false;
    },
    edit(index) {
      this.activeIndex = index;
      this.activeButton = this.staffInfo.callsToAction[index];
      this.view = false;
    },
    save() {
      if (!this.staffInfo.callsToAction) {
        this.staffInfo.callsToAction = [];
      }
      if (
        this.staffInfo.callsToAction[this.activeIndex] &&
        this.activeIndex !== null
      ) {
        // Update an existing record
        this.staffInfo.callsToAction[this.activeIndex].type =
          this.activeButton.type;
        this.staffInfo.callsToAction[this.activeIndex].url =
          this.activeButton.url;
        this.staffInfo.callsToAction[this.activeIndex].label =
          this.activeButton.label;
      } else {
        // Push a new record
        this.staffInfo.callsToAction.push({
          type: this.activeButton.type,
          url: this.activeButton.url,
          label: this.activeButton.label,
        });
      }
      this.view = true;
    },
    remove(index) {
      this.activeButton = {};
      if (this.staffInfo.callsToAction.length > 0) {
        this.staffInfo.callsToAction.splice(index, 1);
      } else {
        this.staffInfo.callsToAction = [];
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
.staffh_calls_to_action-list {
  list-style: none;
}

.staffh_calls_to_action-list li {
  display: flex;
  justify-content: space-between;
  padding: 10px 10px 10px 5px;
  line-height: 1;
}

.staffh_calls_to_action-list .cta-label {
  flex-grow: 1;
  display: inline-block;
  padding-left: 1em;
}
.staffh_calls_to_action-list .grip-dots img {
  width: 6px;
  opacity: 0.25;
  cursor: grab;
  vertical-align: middle;
}

.staffh_calls_to_action-list li button {
  font-size: 10px;
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

<template>
  <div class="modal" @click="closeModal">
    <div class="modal-content">
      <div class="modal-inner_content">
        <!-- Modal content here -->
        <div class="confirm-action" v-if="confirm">
          <pre>{{ this.contact }}</pre>
          <h2>Delete {{ contact.name }}?</h2>
          <p>Are you sure you want to delete this item?</p>
          <p>
            <strong>Warning:</strong> This action is irreversible and the item
            will be deleted permanently.
          </p>
          <div class="modal-actions">
            <button type="reset" class="button" @click="closeModal">
              Cancel
            </button>
            <button type="button" class="button delete" @click="deleteContact">
              Delete
            </button>
          </div>
        </div>
        <form @submit.prevent="saveContact" v-if="!confirm">
          <h2>Quick Contact Information</h2>
          <div class="form-group">
            <label for="name">Name</label>
            <input
              type="text"
              id="name"
              v-model="temporaryContact.name"
              required
            />
          </div>
          <div class="form-group">
            <label for="title">Title</label>
            <input
              type="text"
              id="title"
              v-model="temporaryContact.title"
              required
            />
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              placeholder="yourname@example.com"
              v-model="temporaryContact.email"
              required
            />
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input
              type="tel"
              id="phone"
              placeholder="(555) 123-4567"
              v-model="temporaryContact.phone"
              required
            />
          </div>
          <TagInput
            :label="tagsTitle"
            :temporaryContact="temporaryContact"
            @add-tag="handleAddTag"
            @remove-tag="handleRemoveTag"
            @clear-all="handleClearTags"
          />
          <div class="modal-actions">
            <button type="reset" class="button" @click="closeModal">
              Cancel
            </button>
            <button type="submit" class="button">Save</button>
          </div>
          <div class="debug" style="display: flex">
            <pre>{{ this.temporaryContact }}</pre>
            <pre>{{ this.contact }}</pre>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import TagInput from "./TagInput.vue";

export default {
  name: "Modal",
  components: {
    TagInput,
  },
  props: {
    contact: {
      type: Object,
      required: true,
    },
    confirm: {
      type: Boolean,
    },
  },
  emits: ["update-contact", "save", "delete", "close"],
  data() {
    return {
      tagsTitle: "Link to Staff",
      originalContact: {},
      temporaryContact: {},
    };
  },
  mounted() {
    // Assign the selected staff member to a temporary object for editing (deep copy)
    this.temporaryContact = JSON.parse(JSON.stringify(this.contact));
  },
  methods: {
    handleAddTag(staff) {
      // Add staff member to the temporaryContact object
      this.temporaryContact.staff_members.push(staff);
    },
    handleRemoveTag(tag) {
      // Remove staff member from the temporaryContact object
      const index = this.temporaryContact.staff_members.findIndex(
        (staff) => staff.id === tag
      );
      if (index !== -1) {
        this.temporaryContact.staff_members.splice(index, 1);
      }
    },
    handleClearTags() {
      this.temporaryContact.staff_members = [];
    },
    saveContact() {
      // Emit an event to notify the parent component about the contact update request
      this.$emit("save", this.temporaryContact);
    },
    deleteContact() {
      // Emit an event to notify the parent component about the delete request
      this.$emit("delete", this.temporaryContact);
    },
    closeModal(event) {
      if (event.target === event.currentTarget) {
        this.$emit("close");
      }
    },
  },
};
</script>

<style scoped>
.modal * {
  box-sizing: border-box;
}
.modal {
  /* Styles for the modal overlay */
  background: rgba(0, 0, 0, 0.5);
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 99999;
}

.modal-content {
  /* Styles for the modal content */
  background: #fff;
  position: fixed;
  max-width: 700px;
  min-height: 200px;
  width: 100%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: left;
  padding: 1rem;
}
.modal-inner_content {
  max-height: 90vh;
  overflow-y: scroll;
  padding: 1rem 2rem;
  box-sizing: border-box;
}
.modal-content form label {
  display: block;
}
.modal-content form input,
.modal-content form textarea {
  margin-bottom: 1rem;
  width: 100%;
  border-radius: 4px;
  border: solid 1px #ccc;
  padding: 0.75rem;
}
.modal-actions {
  padding: 1rem 0;
  display: flex;
  gap: 10px;
}
.modal-actions button {
  font-size: 1rem;
  line-height: 1.2;
  border: solid 1px currentColor;
  background-color: #eceff1;
  color: #78909c;
  border-radius: 0.5em;
  padding: 0.5em 1em;
  font-weight: bold;
  cursor: pointer;
}
.modal-actions button:hover {
  color: #333;
}
.modal-actions button[type="submit"] {
  background: #e8f5e9;
  color: #43a047;
}
.modal-actions button[type="submit"]:hover {
  color: #1b5e20;
}
.modal-actions button.delete {
  background: #ffebee;
  color: #e57373;
}
.modal-actions button.delete:hover {
  color: #d50000;
}
</style>

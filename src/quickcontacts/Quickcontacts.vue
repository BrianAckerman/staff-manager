<template>
  <div class="quick_contact_app-header">
    <div class="app-header">
      <div>
        <h2>{{ title }}</h2>
        <p>Add, edit, or delete staff quick contacts.</p>
      </div>
      <div>
        <button
          class="button add"
          type="button"
          title="Add new contact"
          @click="newContact"
        >
          + Add New Contact
        </button>
      </div>
    </div>
    <div v-if="quickcontacts">
      <div class="list-columns">
        <div>
          <div>Name</div>
          <div>Title</div>
          <div>Details</div>
        </div>
        <div>Status</div>
        <div>Actions</div>
      </div>
      <div class="staffh_card-container list">
        <Card
          v-for="contact in quickcontacts"
          :key="contact.id"
          :contact="contact"
          :confirm="confirm"
          @edit="editContact"
          @toggle-status="toggleContactStatus"
          @confirm="confirmAction"
        />
      </div>
      <div>
        <!-- Render the modal component -->
        <Modal
          v-if="showModal"
          :contact="selectedContact"
          :confirm="confirm"
          @close="closeModal"
          @save="saveItem"
          @delete="deleteContact"
        ></Modal>
      </div>
    </div>
    <div v-else>Loading...</div>
  </div>
</template>

<script>
const wpData = window.wpData || {};

import Card from "./components/Card.vue";
import Modal from "./components/Modal.vue";

export default {
  name: "App",
  components: {
    Card,
    Modal,
  },
  data: function () {
    return {
      title: "Quick Contacts",
      quickcontacts: null,
      selectedContact: {},
      showModal: false,
      confirm: false,
    };
  },
  methods: {
    async fetchQuickContacts(method) {
      if (!wpData.canEditPosts) {
        console.warn("User does not have edit_posts capability");
        return;
      }
      // If quickContacts are in the wpData
      if (!method) {
        try {
          this.quickcontacts = wpData.quickContacts.map((contact) => {
            const updatedContact = { ...contact };
            updatedContact.status = contact.status == 1 ? true : false;
            return updatedContact;
          });
        } catch (error) {
          console.error(error);
        }
      } else {
        try {
          const response = await fetch(
            "/wp-json/staff-hero/v1/get-quick-contacts/",
            {
              method: "GET",
              headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": wpData.nonce,
              },
            }
          );

          const data = await response.json();

          if (response.ok) {
            this.quickcontacts = data.map((contact) => {
              const updatedContact = { ...contact };
              updatedContact.status = contact.status == 1 ? true : false;
              return updatedContact;
            });
          } else {
            console.error(
              "Failed to retrieve the quick contacts:",
              data.message
            );
          }
        } catch (error) {
          console.error(
            "An error occurred while retrieving the quick contacts:",
            error
          );
        }
      }
    },
    // Method to open the modal for creating a new contact
    newContact() {
      this.selectedContact = { staff_members: [] }; // Create a new contact - staff_members array requried
      this.showModal = true; // Open the modal
    },
    editContact(contactId) {
      // Find the contact from the quickcontacts and assign it to this.selectedContact
      this.selectedContact = {
        ...this.quickcontacts.find((contact) => contact.id === contactId),
      };
      this.originalContact = this.selectedContact;
      this.showModal = true;
    },
    closeModal() {
      this.editedContact = this.originalContact;
      this.selectedContact = null;
      this.showModal = false;
      this.confirm = false;
    },
    confirmAction(contactId) {
      this.selectedContact = {
        ...this.quickcontacts.find((contact) => contact.id === contactId),
      };
      this.originalContact = this.selectedContact;
      this.confirm = true;
      this.showModal = true;
    },
    async deleteContact(contact) {
      try {
        const response = await fetch(
          `/wp-json/staff-hero/v1/delete-quick-contact/${contact.id}`,
          {
            method: "DELETE",
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": wpData.nonce,
            },
          }
        );
        if (response.ok) {
          this.fetchQuickContacts(true);
          this.confirm = false;
          this.showModal = false;
        } else {
          console.error("Error deleting card");
        }
      } catch (error) {
        console.error("Error deleting card:", error);
      }
    },
    async saveItem(editedContact) {
      // Find the index of the edited contact in the quickcontacts array
      const index = this.quickcontacts.findIndex(
        (contact) => contact.id === editedContact.id
      );

      if (index !== -1) {
        // Update the contact data in the quickcontacts array
        this.quickcontacts[index] = editedContact;
      }
      const updateOrNew =
        typeof editedContact.id !== "undefined"
          ? `/wp-json/staff-hero/v1/update-quick-contact/${editedContact.id}`
          : `/wp-json/staff-hero/v1/new-quick-contact`;

      // Perform the POST request to save the new contact information to the API
      try {
        const response = await fetch(updateOrNew, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": wpData.nonce,
          },
          body: JSON.stringify(editedContact),
        });
        const data = await response.json();
        if (!response.ok) {
          console.error("Failed to save the quick contact:", data.message);
        } else {
          this.fetchQuickContacts(true); // Fetch the updated contacts from the database
        }
      } catch (error) {
        console.error(
          "An error occurred while saving the quick contact:",
          error
        );
      }
      this.showModal = false; // Close the modal
    },
    async toggleContactStatus(contactId) {
      const contact = this.quickcontacts.find((c) => c.id === contactId);
      if (contact) {
        contact.status = !contact.status;
        try {
          const response = await fetch(
            `/wp-json/staff-hero/v1/toggle-quick-contact-status/${contactId}`,
            {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": wpData.nonce,
              },
            }
          );
          const data = await response.json();
          if (!response.ok) {
            console.error("Failed to toggle contact status:", data.message);
            // Revert the status change if the update failed
            contact.status = !contact.status;
          }
        } catch (error) {
          console.error(
            "An error occurred while toggling contact status:",
            error
          );
          // Revert the status change if an error occurred
          contact.status = !contact.status;
        }
      }
    },
  },
  created: function () {
    this.fetchQuickContacts();
  },
};
</script>

<style>
.quick_contact_app-header {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin-top: 60px;
  padding-right: 20px;
}
.app-header {
  display: flex;
  justify-content: space-between;
  padding: 0 2rem;
}
.app-header,
.app-header h2,
.app-header p {
  line-height: 1.2;
  margin: 0;
}
.app-header p {
  color: #78909c;
}

.button.add {
  background: #2962ff;
  color: #fff;
  border-radius: 5px;
  padding: 0.75rem 2rem;
  border: 0 none;
  outline: 0 none;
  font-weight: 600;
  line-height: 1.2;
  cursor: pointer;
}
.button.add:hover {
  background: #2979ff;
  color: #fff;
}

.list-columns {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 5px;
  padding: 3rem 2rem 1rem;
  font-weight: bold;
  color: #78909c;
}
.list-columns > div:first-child {
  flex-grow: 1;
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
}
.list-columns > div:nth-last-child(2) {
  flex-basis: 120px;
}
.list-columns > div:last-child {
  flex-basis: 135px;
}
.staffh_card-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.staffh_card-container.list {
  flex-direction: column;
  gap: 5px;
}
</style>

<template>
  <div class="staffh_card" :class="{ disabled: !contact.status }">
    <div class="staffh_card-item">
      <div class="staffh_card-item__content">
        <div class="staffh_card-item__content-name">{{ contact.name }}</div>
        <div class="staffh_card-item__content-title">{{ contact.title }}</div>
        <div class="staffh_card-item__content-details">
          Email: {{ contact.email }}<br />
          Phone: {{ contact.phone }}
        </div>
      </div>
      <div class="staffh_card-item__toggle">
        <button
          type="button"
          title="Enable"
          :class="{ inactive: !contact.status }"
          class="button-toggle"
          @click="toggleStatus"
        >
          {{ contact.status === false ? "Inactive" : "Active" }}
        </button>
      </div>
      <div class="staffh_card-item__actions">
        <button type="button" class="button" @click="openEditModal">
          Edit
        </button>
        <button type="button" class="button delete" @click="openConfirmModal">
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Card",
  props: {
    contact: {
      type: Object,
      required: true,
    },
    confirm: {
      type: Boolean,
    },
  },
  emits: ["toggle-status", "delete-item", "edit"],
  data() {
    return {
      showModal: false,
    };
  },
  methods: {
    openEditModal() {
      // Implement logic to open the edit modal
      this.$emit("edit", this.contact.id);
    },
    openConfirmModal() {
      this.$emit("confirm", this.contact.id);
    },
    closeModal() {
      // Implement logic to close the modal
      this.showModal = false;
    },
    toggleStatus() {
      this.$emit("toggle-status", this.contact.id);
    },
    deleteItem() {
      this.$emit("delete-item", this.contact.id);
    },
  },
};
</script>

<style scoped>
.staffh_card * {
  box-sizing: border-box;
}
.staffh_card {
  color: #000;
  background: #fff;
  border-radius: 10px;
  flex-grow: 1;
  padding: 0.5rem 2rem;
  text-align: left;
  position: relative;
}

.staffh_card:hover {
  background: rgb(237, 246, 253);
}

.staffh_card.disabled .staffh_card-item__content {
  opacity: 0.25;
}

h2,
h3,
h4,
h5,
p {
  margin: 0;
}
.staffh_card-item,
.staffh_card-item__content {
  display: flex;
  align-items: center;
  gap: 5px;
  justify-content: flex-start;
}
.staffh_card-item__content {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  flex-grow: 1;
  font-weight: bold;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.staffh_card-item__toggle {
  flex-basis: 120px;
}
.staffh_card-item__content-name {
  font: 1.25rem/1.5rem inherit;
}
.staffh_card-item__content-title {
  font: 1.25rem/1.5rem inherit;
}
.staffh_card-item__content-details {
  font-size: 0.75rem !important;
  font-weight: 400;
  line-height: 1.25rem;
  letter-spacing: 0.0333333333em !important;
  text-transform: none !important;
}

.staffh_card-item__actions {
  align-items: center;
  display: flex;
  flex-basis: 135px;
  min-height: 52px;
  gap: 5px;
}

.staffh_card-item__actions .button {
  color: #42a5f5;
  border: solid 1px currentColor;
  background-color: #e3f2fd;
  border-radius: 0.5em;
  padding: 0.5em 1em;
  font-size: 0.75rem;
  line-height: 1.2;
  font-weight: bold;
  cursor: pointer;
}
.staffh_card-item__actions .button:hover {
  color: #2962ff;
}
.staffh_card-item__actions .button.delete {
  background: #ffebee;
  color: #e57373;
}
.staffh_card-item__actions .button.delete:hover {
  color: #d50000;
}
.button-toggle {
  opacity: 0.25;
  padding: 0.5rem;
  color: #009688;
  background: #e0f2f1;
  border: solid 1px currentColor;
  border-radius: 5em;
  padding: 0.5em 1em;
  opacity: 0.75;
  font-size: 0.75rem;
  line-height: 1.2;
  font-weight: bold;
  letter-spacing: 0.75px;
  margin: 5px;
  cursor: pointer;
}
.button-toggle.inactive {
  color: #ff6f00;
  background-color: #fff8e1;
}
.button-toggle:hover {
  opacity: 1;
}
</style>

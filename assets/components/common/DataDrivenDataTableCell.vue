<template>
  <div>
    <span v-if="type === 'Text'">
      {{ value || '-' }}
    </span>
    <span
        v-else-if="type === 'RichText'"
        style="white-space: pre-wrap;"
    >{{ value || '-' }}</span>
    <span v-else-if="type === 'List'">
      {{ listValue }}
    </span>
    <span v-else-if="type === 'DemolitionDateHeader'">
      {{ dateText || '-' }}
    </span>
    <span v-else-if="type === 'EnumField'">
      {{ listValue || '-' }}
    </span>
    <span v-else-if="type === 'Memberfield'">
      {{ value ? value.name : '-' }}
    </span>
    <span v-else>
      {{ value }}
    </span>
  </div>
</template>

<script>
const moment = require('moment');

export default {
  name: "DataDrivenDataTableCell",
  props: {
    type: {
      required: true
    },
    header: {
      required: true
    },
    value: {
      required: true
    },
    listItems: {
      required: false,
      default: []
    }
  },

  data() {
    return {
      dateText: null
    };
  },

  computed: {
    listValue() {
      const result = this.listItems.find(item => item.key === this.value);
      return result[this.header.listValue || 'value'] || '-';
    }
  }
}
</script>

<style scoped>

</style>
<template>
  <div>
    <data-driven-data-table
        schema="MemberSchema"
        :search="search"
    />
  </div>
</template>

<script>
import DataDrivenDataTable from "../common/DataDrivenDataTable";

export default {
  name: "Members",
  components: {DataDrivenDataTable},

  data() {
    return {
      search: ""
    };
  }
}
</script>

<style scoped>

</style>
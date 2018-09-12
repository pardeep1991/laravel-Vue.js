<template>
    <el-container>
        <el-header>
            <el-row>
                <el-col :span="3" class="logo"> 
                    <span><i class="el-icon-search"></i> URL Finder</span>
                </el-col>
                <el-col :span="21">
                    <el-menu :default-active="active" mode="horizontal" @select="handleSelect">
                        <el-menu-item index="keywords">Keywords</el-menu-item>
                        <el-menu-item index="tasks">Tasks</el-menu-item>
                        <el-menu-item index="results">Results</el-menu-item>
                    </el-menu>
                </el-col>
            </el-row>
        </el-header>
        <el-main>
            <div v-if="active === 'keywords'">
                <keywords></keywords>
            </div>
            <div v-else-if="active === 'tasks'">
                <tasks></tasks>
            </div>
            <div v-else-if="active === 'results'">
                <results></results>
            </div>
            <div v-else>
                Not Found
            </div>
        </el-main>
    </el-container>
</template>
<style>
body {
    margin: 0
}
.logo {
    display: block;
    line-height: 60px;
    text-align: center;
    font-weight: bold;
    border-bottom: solid 1px #e6e6e6;
}
</style>
<script>
export default {
    data() {
      return {
        active: 'keywords'
      };
    },
    methods: {
      handleSelect(key, keyPath) {
        this.active = key
      }
    }
}
</script>


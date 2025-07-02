<template>
  <div class="p-4">
    <a-row :gutter="[32, 32]" justify="center">
      <!-- Start Operation -->
      <a-col :xs="24" :sm="12" :md="8" :lg="6">
        <a-card hoverable class="home-card" @click="to('/production/declare')">
          <template #cover>
            <PlayCircleOutlined class="home-icon" />
          </template>
          <a-card-meta title="Démarrer Opération" />
        </a-card>
      </a-col>
      <a-col :xs="24" :sm="12" :md="8" :lg="6">
        <a-card hoverable class="home-card" @click="to('/login')">
          <template #cover>
            <LogoutOutlined class="home-icon-danger" />
          </template>
          <a-card-meta title="Se Déconnecter" />
        </a-card>
      </a-col>

    </a-row>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import {
  PlayCircleOutlined,
  LogoutOutlined

} from '@ant-design/icons-vue';

const router = useRouter();
const to = (path) => router.push(path);
</script>

<style scoped>
.home-card {
  text-align: center;
  border-radius: 16px;
  transition: transform 0.2s;
}
.home-card:hover {
  transform: translateY(-4px);
}
.home-icon {
  font-size: 3rem;        /* big icon */
  padding: 32px 0;
  color: #1890ff;         /* Ant Design primary */
}
.home-icon-danger {
  font-size: 3rem;        /* big icon */
  padding: 32px 0;
  color: #ff2222;         /* Ant Design primary */
}
</style>

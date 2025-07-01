<template>
  <div class="p-4">
    <a-row :gutter="[32, 32]" justify="center">
      <!-- Declare Operations -->
      <a-col :xs="24" :sm="12" :md="8" :lg="6">
        <a-card hoverable class="home-card" @click="to('/operations/')">
          <template #cover>
            <ToolOutlined class="home-icon" />
          </template>
          <a-card-meta title="Déclarer Opérations" />
        </a-card>
      </a-col>

      <!-- Start Operation -->
      <a-col :xs="24" :sm="12" :md="8" :lg="6">
        <a-card hoverable class="home-card" @click="to('/production/declare')">
          <template #cover>
            <PlayCircleOutlined class="home-icon" />
          </template>
          <a-card-meta title="Démarrer Opération" />
        </a-card>
      </a-col>

      <!-- Finish Operation -->
      <a-col :xs="24" :sm="12" :md="8" :lg="6">
        <a-card hoverable class="home-card" @click="to('/operations/finish')">
          <template #cover>
            <CheckCircleOutlined class="home-icon" />
          </template>
          <a-card-meta title="Terminer Opération" />
        </a-card>
      </a-col>

      <!-- Transfer Material -->
      <a-col :xs="24" :sm="12" :md="8" :lg="6">
        <a-card hoverable class="home-card" @click="to('/mp/transfers/transfer')">
          <template #cover>
            <SwapOutlined class="home-icon" />
          </template>
          <a-card-meta title="Transférer Matière" />
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import {
  ToolOutlined,
  PlayCircleOutlined,
  CheckCircleOutlined,
  SwapOutlined,
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
</style>

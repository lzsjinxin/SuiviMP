<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"

export default {
    name: "SAMPLE-PAGE",
    components: {
        Layout, pageheader
    }
}
</script>

<template>
    <Layout>
        <pageheader title="Sample Page" pageTitle="Other" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardHeader>
                        <h5>this is a 404 page</h5>
                    </BCardHeader>
                    <BCardBody>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
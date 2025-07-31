import { mount, flushPromises } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import NavBar from '../NavBar.vue';
import axios from 'axios';

vi.mock('axios');

const auth = { isAuthenticated: { value: false } };
vi.mock('../../auth.js', () => auth);

vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
  useRoute: () => ({ path: '/' })
}));

describe('NavBar', () => {
  it('shows team link after admin login', async () => {
    axios.get.mockResolvedValue({ data: { role: 'Admin' } });

    const wrapper = mount(NavBar);
    await flushPromises();

    expect(wrapper.find('a[href="/teams"]').exists()).toBe(false);

    auth.isAuthenticated.value = true;
    await flushPromises();

    expect(wrapper.find('a[href="/teams"]').exists()).toBe(true);
  });
});

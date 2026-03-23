import axios from 'axios';

const WP_URL = process.env.WP_URL as string;
const WP_USER = process.env.WP_USER as string;
const WP_APP_PASSWORD = process.env.WP_APP_PASSWORD as string;

function getAuthHeader() {
  const token = Buffer.from(`${WP_USER}:${WP_APP_PASSWORD}`).toString('base64');
  return `Basic ${token}`;
}

export async function wordpressExecute(action: string, payload: any) {
  switch (action) {
    case 'create_post':
      return createPost(payload);

    default:
      throw new Error(`Unsupported action: ${action}`);
  }
}

async function createPost(payload: any) {
  const response = await axios.post(
    `${WP_URL}/wp-json/wp/v2/posts`,
    payload,
    {
      headers: {
        Authorization: getAuthHeader(),
        'Content-Type': 'application/json'
      }
    }
  );

  return response.data;
}

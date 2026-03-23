import 'dotenv/config';
import app from './server';

const port = Number(process.env.PORT || 5001);

app.listen(port, () => {
  console.log(`ACC WP Adapter running on port ${port}`);
});

import { NextApiRequest, NextApiResponse } from 'next';
import jwt from 'jsonwebtoken';
import { getUserByEmail, verifyPassword } from '..//auth'; // Zakładam, że masz takie funkcje

const secret = 'your_jwt_secret'; // Powinieneś trzymać to w zmiennej środowiskowej

export default async (req: NextApiRequest, res: NextApiResponse) => {
    const { username, password } = req.body;

    const user = await getUserByEmail(username);

    if (!user || !await verifyPassword(user, password)) {
        return res.status(401).json({ status: 'error', message: 'Invalid credentials' });
    }

    const token = jwt.sign({ id: user.id, email: user.email }, secret, { expiresIn: '1h' });

    res.status(200).json({ status: 'success', token });
};

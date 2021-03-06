import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

/**
 * User model
 */
export default class User extends Model {
  static type() {
    return 'users';
  }

  static fields() {
    return {
      first_name: new Attribute(),
      last_name: new Attribute(),
      password: new Attribute(),
      email: new Attribute(),
      remark: new Attribute(),
      last_login: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static computed() {
    return {
      name(user) {
        return user.first_name + ' ' + user.last_name;
      },
      lastLoginFormatted(story) {
        if (story.last_login) {
          return story.last_login.format('L HH:mm');
        }
        return '';
      },
      createdAtFormatted(story) {
        if (story.created_at) {
          return story.created_at.format('L HH:mm');
        }
        return '';
      },
    };
  }

  async createWithToken(token) {
    var data = this.serialize();
    const requestConfig = {
      method: 'POST',
      url: `${this.resourceUrl()}/${token}`,
      data: data,
    };
    let response = await this.request(requestConfig);
    return this.respond(response);
  }
}
